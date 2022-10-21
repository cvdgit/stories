<?php

declare(strict_types=1);

namespace common\services\auth;

use common\components\ModelDomainException;
use common\helpers\EmailHelper;
use common\rbac\UserRoles;
use common\services\RoleManager;
use DomainException;
use Exception;
use frontend\components\queue\UnisenderAddJob;
use frontend\models\auth\CreateUserForm;
use RuntimeException;
use Yii;
use common\models\User;
use common\services\TransactionManager;

class SignupService
{
    private $transactionManager;
    private $roleManager;

    public function __construct(TransactionManager $transactionManager, RoleManager $roleManager)
    {
        $this->transactionManager = $transactionManager;
        $this->roleManager = $roleManager;
    }

    public function signup(string $username, string $email, string $password): void
    {
        $user = User::createSignup($username, $email, $password);
        $this->transactionManager->wrap(function() use ($user) {

            if (!$user->save()) {
                throw new DomainException('Ошибка при сохранении пользователя');
            }

            $this->roleManager->assign($user->id, UserRoles::ROLE_USER);

            $user->createMainStudent();
        });
    }

    public function signupSocial(CreateUserForm $form): User
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $user = User::createSignup(
            $form->username,
            $form->email,
            $form->password
        );
        $user->status = User::STATUS_ACTIVE;

        $this->transactionManager->wrap(function() use ($user) {

            if (!$user->save()) {
                throw ModelDomainException::create($user);
            }

            $this->roleManager->assign($user->id, UserRoles::ROLE_USER);

            $user->createMainStudent();
        });

        return $user;
    }

    public function sentEmailConfirm(User $user): void
    {
        $response = EmailHelper::sendEmail($user->email, 'Для завершения регистрации подтвердите свой email', 'userSignupComfirm-html', ['user' => $user]);
        if (!$response->isSuccess()) {
            throw new RuntimeException('Confirm email sent error - ' . $response->getError()->getMessage());
        }
    }

    public function sendWelcomeEmail(User $user): void
    {
        $response = EmailHelper::sendEmail($user->email, 'Добро пожаловать на Wikids', 'userWelcome-html', ['user' => $user]);
        if (!$response->isSuccess()) {
            throw new RuntimeException('Welcome email sent error');
        }
    }

    public function addJob(int $userID)
    {
        Yii::$app->queue->push(new UnisenderAddJob([
            'userID' => $userID,
        ]));
    }

    public function activateFreeSubscription(User $user)
    {
        //$this->paymentService->createFreeOneYearSubscription($user->id);
    }

    public function afterUserSignup(User $user)
    {
        $this->transactionManager->wrap(function() use ($user) {
            $this->sendWelcomeEmail($user);
            $this->activateFreeSubscription($user);
            $this->addJob($user->id);
        });
    }

    public function signupWithConfirmEmail(string $username, string $email, string $password): void
    {
        $this->signup($username, $email, $password);

        if (($user = User::findByEmail($email)) === null) {
            throw new DomainException('Пользователь с указанным email не найден');
        }

        $this->sentEmailConfirm($user);
    }
}
