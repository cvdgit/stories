<?php

namespace common\services;

use DomainException;
use common\models\Payment;
use common\models\SubscriptionModel;

class UserPaymentService
{

	public function activateSubscription($userID, SubscriptionModel $model): void
	{
		$payment = Payment::create(
			$userID,
			$model->subscription_id,
			$model->date_start,
            $model->date_finish,
			$model->state
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
