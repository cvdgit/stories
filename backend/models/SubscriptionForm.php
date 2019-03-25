<?php

namespace backend\models;

class SubscriptionForm extends \yii\base\Model
{

	public $subscription_id;
	public $date_start;
	public $date_finish;
    public $state;

    public function rules()
    {
        return [
            [['subscription_id', 'date_start', 'date_finish'], 'required'],
            [['subscription_id'], 'integer'],
            ['subscription_id', 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Rate::class, 'targetAttribute' => ['subscription_id' => 'id']],
            [['date_start', 'date_finish'], 'date', 'format' => 'yyyy.MM.dd'],
            ['state', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
        	'subscription_id' => 'Подписка',
            'date_start' => 'Дата начала подписки',
            'date_finish' => 'Дата окончания подписки',
            'state' => 'Статус',
        ];
    }

}
