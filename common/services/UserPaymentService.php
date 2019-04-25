<?php

namespace common\services;

use common\models\Rate;
use DateInterval;
use DateTime;
use DomainException;
use common\models\Payment;

class UserPaymentService
{

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
