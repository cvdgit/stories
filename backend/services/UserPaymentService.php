<?php

namespace backend\services;

use common\models\Payment;

class UserPaymentService
{

	public function activateSubscription($userID, $subscriptionModel)
	{
		$payment = Payment::create(
			$userID,
			$subscriptionModel->subscription_id,
			$subscriptionModel->date_start,
			$subscriptionModel->date_finish,
			$subscriptionModel->state
		);
		$payment->save(false);
	}

	public function cancelSubscription($subscriptionID)
	{
        $subscription = Payment::findOne($subscriptionID);
        if ($subscription === null) {
			throw new \DomainException('Подписка не найдена.');
        }
        $subscription->state = Payment::STATUS_INVALID;
        $subscription->save(false, ['state']);
	}

}
