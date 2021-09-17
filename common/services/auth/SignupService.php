<?php

namespace common\services\auth;

use common\helpers\EmailHelper;
use Exception;
use frontend\components\queue\UnisenderAddJob;
use RuntimeException;
use Yii;
use common\models\User;
use common\services\TransactionManager;

class SignupService
{

    protected $transaction;

    public function __construct(TransactionManager $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param $username
     * @param $email
     * @param $password
     * @throws Exception
     */
    public function signup($username, $email, $password): void
    {
        $user = User::createSignup(
            $username,
            $email,
            $password
        );
        $this->transaction->wrap(function() use ($user) {

            /* @var $user User */
            $user->save();

            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('user');
            $auth->assign($authorRole, $user->getId());

            $user->createMainStudent();
        });
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
        $this->transaction->wrap(function() use ($user) {
            $this->sendWelcomeEmail($user);
            $this->activateFreeSubscription($user);
            $this->addJob($user->id);
        });
    }

}