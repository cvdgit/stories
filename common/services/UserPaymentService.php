<?php

namespace common\services;

use common\models\Rate;
use common\models\User;
use DomainException;
use common\models\Payment;
use frontend\models\SubscriptionForm;
use RuntimeException;
use Yii;
use yii\helpers\Json;

class UserPaymentService
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getRates()
    {
        return Rate::find()->all();
    }

    public function activateSubscription($userID, SubscriptionForm $model): int
    {
        $user = User::findModel($userID);
        if (!$user->isActive()) {
            throw new DomainException('Создание платежа для не активного пользователя');
        }

        $subscription = Rate::findModel($model->subscription_id);
        if ($subscription->isArchive()) {
            throw new DomainException('Создание платежа для архивной подписки');
        }

        $payment = Payment::create(
            $user->id,
            $subscription->id,
            $model->start_date,
            $model->finish_date,
            Payment::STATUS_NEW
        );

        if ($subscription->isFreeSubscription()) {
            $payment->state = Payment::STATUS_VALID;
            $payment->save();
            $this->sendEmailActivate($user, $subscription);
        }
        else {
            $payment->save();
        }
        return $payment->id;
    }

	protected function freeSubscriptionCheck($userID, Rate $rate): void
    {
        if ($rate->isFreeSubscription() && $this->userService->hasFreeSubscription($userID)) {
            throw new DomainException('Бесплатная подписка уже была активирована');
        }
    }

    protected function sendEmailActivate(User $user, Rate $rate): void
    {
        $sent = Yii::$app->mailer
            ->compose(['html' => 'userActivateSub-html', 'text' => 'userActivateSub-text'], ['user' => $user, 'rate' => $rate])
            ->setTo($user->email)
            ->setFrom([Yii::$app->params['infoEmail'] => Yii::$app->name])
            ->setSubject('Активирована подписка на wikids.ru')
            ->send();
        if (!$sent) {
            throw new RuntimeException('Ошибка при отправке email об активации подписки');
        }
    }

    protected function sendEmailCancel(Payment $payment): void
    {
        /** @var $user User */
        $user = $payment->user;
        $sent = Yii::$app->mailer
            ->compose(['html' => 'userCancelSub-html', 'text' => 'userCancelSub-text'], ['user' => $user, 'rate' => $payment->rate])
            ->setTo($user->email)
            ->setFrom([Yii::$app->params['infoEmail'] => Yii::$app->name])
            ->setSubject('Закончилась подписка на wikids.ru')
            ->send();
        if (!$sent) {
            throw new RuntimeException('Ошибка при отправке email об отмене подписки');
        }
    }

    public function generateToken($args): string
    {
        if (isset($args['Token'])) {
            unset($args['Token']);
        }
        $args['Password'] = Yii::$app->params['terminalPassword'];
        ksort($args);
        $token = '';
        foreach ($args as $arg) {
            if (!is_array($arg)) {
                $token .= var_export($arg, true);
            }
        }
        $token = str_replace("'", '', $token);
        $token = hash('sha256', $token);
        return $token;
    }

    public function checkToken($args, $expectedToken): bool
    {
        $actualToken = $args['Token'];
        return !($expectedToken === null || strcasecmp($actualToken, $expectedToken) !== 0);
    }

	public function cancelSubscription($paymentID): void
	{
        $payment = Payment::findModel($paymentID);
        $payment->state = Payment::STATUS_INVALID;
        $payment->save(false, ['state']);
        $this->sendEmailCancel($payment);
	}

    public function processPaymentNotify($args): void
    {
        $paymentID = $args['OrderId'];
        $payment = Payment::findModel($paymentID);
        if ($args['Status'] === 'CONFIRMED') {
            $payment->state = Payment::STATUS_VALID;
        }
        if (empty($payment->data)) {
            $payment->data = Json::encode($args);
        }
        else {
            $payment->data = $payment->data . "\n\n" . Json::encode($args);
        }
        $payment->save(false);
        if ($payment->isValid()) {
            $this->sendEmailActivate($payment->user, $payment->rate);
        }
    }

}
