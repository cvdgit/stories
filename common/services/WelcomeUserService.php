<?php


namespace common\services;


use common\helpers\EmailHelper;
use common\models\User;
use frontend\components\queue\UnisenderAddJob;
use Yii;

class WelcomeUserService
{

    protected $transactionManager;
    protected $paymentService;

    public function __construct(TransactionManager $transactionManager, UserPaymentService $paymentService)
    {
        $this->transactionManager = $transactionManager;
        $this->paymentService = $paymentService;
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
        $this->paymentService->createFreeOneYearSubscription($user->id);
    }

    public function afterUserSignup(User $user)
    {
        $this->transactionManager->wrap(function() use ($user) {
            $this->sendWelcomeEmail($user);
            $this->activateFreeSubscription($user);
            $this->addJob($user->id);
        });
    }

}