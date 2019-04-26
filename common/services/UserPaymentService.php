<?php

namespace common\services;

use common\models\Rate;
use common\models\User;
use DateInterval;
use DateTime;
use DomainException;
use common\models\Payment;
use RuntimeException;
use Yii;

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

    public function getStartDate()
    {
        return date('Y-m-d H:i:s');
    }

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

	public function cancelSubscription($subscriptionID): void
	{
        $subscription = Payment::findOne($subscriptionID);
        if ($subscription === null) {
			throw new DomainException('Подписка не найдена.');
        }
        $subscription->state = Payment::STATUS_INVALID;
        $subscription->save(false, ['state']);
	}

}
