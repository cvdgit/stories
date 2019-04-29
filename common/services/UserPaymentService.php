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

    /*
    public function getStartDate()
    {
        return date('Y-m-d H:i:s');
    }
    */

    public function createPayment($userID, SubscriptionForm $model)
    {
        $payment = Payment::create(
            $userID,
            $model->subscription_id,
            $model->start_date,
            $model->finish_date,
            Payment::STATUS_NEW
        );
        $payment->save();
        return $payment->id;
    }

    /*
    public function getFinishDate($subscriptionID, $startDate): string
    {
        $rate = Rate::findOne($subscriptionID);
        $date = new DateTime($startDate);
        return $date->add(new DateInterval("P{$rate->days}D"))->format('Y-m-d H:i:s');
    }

	public function activateSubscription($userID, $subscriptionID): void
	{
        $rate = Rate::findOne($subscriptionID);
	    $this->freeSubscriptionCheck($userID, $rate);
	    $startDate = $this->getStartDate();
	    $finishDate = $this->getFinishDate($subscriptionID, $startDate);
		$payment = Payment::create(
			$userID,
            $subscriptionID,
            $startDate,
            $finishDate,
            Payment::STATUS_VALID
		);
		$payment->save(false);

		$user = $this->userService->findUserByID($userID);
		$this->sendEmailActivate($user, $rate);
	}
    */

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
                $token .= $arg;
            }
        }
        $token = hash('sha256', $token);
        return $token;
    }

    public function checkToken($args, $expectedToken): bool
    {
        $actualToken = $args['Token'];
        return !($expectedToken === null || strcasecmp($actualToken, $expectedToken) !== 0);
    }

    /*
	public function cancelSubscription($subscriptionID): void
	{
        $subscription = Payment::findOne($subscriptionID);
        if ($subscription === null) {
			throw new DomainException('Подписка не найдена.');
        }
        $subscription->state = Payment::STATUS_INVALID;
        $subscription->save(false, ['state']);
	}
	*/

    /**
     * @param $id
     * @return Payment|null
     */
    public function findPaymentByID($id): ?Payment
    {
        if (($payment = Payment::findOne($id)) !== null) {
            return $payment;
        }
        throw new DomainException('Данные об оплате не найдены.');
    }

    public function processPaymentNotify($args): void
    {
        $paymentID = $args['OrderId'];
        /** @var $payment Payment */
        $payment = $this->findPaymentByID($paymentID);

        $status = ($args['Status'] === 'CONFIRMED' ? Payment::STATUS_VALID : Payment::STATUS_INVALID);
        $payment->state = $status;
        $payment->data = Json::encode($args);
        $payment->save();
    }

}
