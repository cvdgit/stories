<?php

namespace common\services;

use backend\models\UserCreateForm;
use backend\models\UserUpdateForm;
use common\models\User;
use common\rbac\UserRoles;
use common\services\auth\SignupService;
use Exception;
use Yii;
use yii\web\NotFoundHttpException;
use common\models\PaymentQuery;

class UserService
{

    protected $transaction;
    protected $roleManager;
    protected $signupService;

    public function __construct(TransactionManager $transaction,
                                RoleManager $roleManager,
                                SignupService $signupService)
    {
        $this->transaction = $transaction;
        $this->roleManager = $roleManager;
        $this->signupService = $signupService;
    }

    /**
     * @param $id
     * @return User|null
     * @throws NotFoundHttpException
     */
    public function findUserByID($id): ?User
    {
        if (($user = User::findOne($id)) !== null) {
            return $user;
        }
        throw new NotFoundHttpException('Пользователь не найден.');
    }

    /**
     * @param $userID
     * @return bool
     * @throws NotFoundHttpException
     */
    public function hasSubscription($userID): bool
    {
        /* @var $user User */
        $user = $this->findUserByID($userID);
        return $user->hasSubscription();
    }

    /**
     * @param $userID
     * @return bool
     * @throws NotFoundHttpException
     */
    public function hasFreeSubscription($userID): bool
    {
        /* @var $user User */
        $user = $this->findUserByID($userID);
        /* @var $payments PaymentQuery */
        $payments = $user->getPayments();
        return $payments->freeSubscription()->exists();
    }

    /**
     * @param $userID
     * @return bool
     * @throws NotFoundHttpException
     */
    public function hasValidFreeSubscription($userID): bool
    {
        /* @var $user User */
        $user = $this->findUserByID($userID);
        /* @var $payments PaymentQuery */
        $payments = $user->getPayments();
        return $payments->freeSubscription()->exists();
    }

    public function create(UserCreateForm $form): User
    {
        $user = User::create(
            $form->username,
            $form->email,
            $form->password
        );
        $this->transaction->wrap(function () use ($user, $form) {
            $user->save();
            $this->roleManager->assign($user->id, $form->role);
            $user->createMainStudent();
        });

        try {
            $this->signupService->sendWelcomeEmail($user);
        }
        catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
        }
        $this->signupService->addJob($user->id);

        return $user;
    }

    public function edit(int $userID, UserUpdateForm $form)
    {
        $user = User::findModel($userID);
        $user->edit(
            $form->username,
            $form->email,
            $form->status
        );
        $this->transaction->wrap(function () use ($user, $form) {
            $user->save();
            $this->roleManager->assign($user->id, $form->role);
        });
    }

    public function createFromGroup(array $data): array
    {

        $forCreate = [];
        $result = [];

        foreach ($data as $userData) {
            $user = User::findByEmail($userData->email);
            if ($user === null) {
                $forCreate[] = $userData;
            }
            else {
                $result[] = $user->id;
                if (!$this->roleManager->canUser($user->id, UserRoles::ROLE_STUDENT)) {
                    $this->roleManager->revoke($user->id);
                    $this->roleManager->assign($user->id, UserRoles::ROLE_STUDENT);
                }
                if (!empty($userData->firstname) && !empty($userData->lastname)) {
                    $user->updateProfile($userData->firstname, $userData->lastname);
                }
            }
        }

        foreach ($forCreate as $userData) {

            $this->transaction->wrap(function() use ($userData, &$result) {

                $user = User::create(
                    User::createUsername(),
                    $userData->email,
                    User::createPassword()
                );
                $user->save();

                $this->roleManager->assign($user->id, UserRoles::ROLE_STUDENT);

                $user->createMainStudent();

                //if (!empty($userData->firstname) && !empty($userData->lastname)) {
                    $profile = $user->updateProfile($userData->firstname, $userData->lastname);
                    $profile->save();
                //}

                $result[] = $user->id;
            });
        }

        return $result;
    }
}