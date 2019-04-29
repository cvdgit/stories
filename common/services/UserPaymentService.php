<?php

namespace common\services;

use common\models\Rate;
use common\models\User;
use DateInterval;
use DateTime;
use DomainException;
use common\models\Payment;
use frontend\models\SubscriptionForm;
use RuntimeException;
use Yii;
use yii\helpers\Json;

class UserPaymentService
{

    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function getRates()
    {
        return Rate::find()->all();
    }

    public function createPayment($userID, SubscriptionForm $model): int
    {
        $payment = Payment::create(
            $userID,
            $model->subscription_id,
            $model->start_date,
            $model->finish_date,
            Payment::STATUS_NEW
        );
        $payment->save();

        //$user = $this->userService->findUserByID($userID);
        //$this->sendEmailActivate($user, $rate);

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
            ->setFrom(Yii::$app->params['infoEmail'])
            ->setSubject('Активирована подписка на wikids.ru')
            ->send();
        if (!$sent) {
            throw new RuntimeException('Ошибка при отправке email об активации подписки');
        }
    }

    protected function sendEmailCancel(User $user, Rate $rate): void
    {
        $sent = Yii::$app->mailer
            ->compose(['html' => 'userCancelSub-html', 'text' => 'userCancelSub-text'], ['user' => $user, 'rate' => $rate])
            ->setTo($user->email)
            ->setFrom(Yii::$app->params['infoEmail'])
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

	public function cancelSubscription($subscriptionID): void
	{
        $payment = $this->findPaymentByID($subscriptionID);
        $payment->state = Payment::STATUS_INVALID;
        $payment->save(false, ['state']);

        //$user = $this->userService->findUserByID($userID);
        //$this->sendEmailCancel($user, $rate);
	}

    /**
     * @param $id
     * @return Payment
     */
    public function findPaymentByID($id): Payment
    {
        if (($payment = Payment::findOne($id)) !== null) {
            return $payment;
        }
        throw new DomainException('Данные об оплате не найдены.');
    }

    public function processPaymentNotify($args): void
    {
        $paymentID = $args['OrderId'];
        $payment = $this->findPaymentByID($paymentID);
        $status = ($args['Status'] === 'CONFIRMED' ? Payment::STATUS_VALID : Payment::STATUS_INVALID);
        $payment->state = $status;
        $payment->data = Json::encode($args);
        $payment->save(false);
    }

}
