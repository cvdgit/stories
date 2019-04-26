<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\Expression;

class PaymentQuery extends ActiveQuery
{
	
	public function paymentsByUser($userID)
	{
		return $this->andWhere(['{{%payment}}.user_id' => $userID]);
	}

	public function validPayments()
	{
		return $this->andWhere('state = :state', [':state' => Payment::STATUS_VALID])
		            ->andWhere(['between', new Expression('NOW()'),
		            	                   new Expression('payment'),
		            	                   new Expression('finish')]);
	}

	public function isValid()
    {
        return $this->andWhere('state = :state', [':state' => Payment::STATUS_VALID]);
    }

	public function freeSubscription()
    {
        return $this->joinWith('rate')->andWhere('{{%rate}}.code = :code', [':code' => 'free']);
    }

}
