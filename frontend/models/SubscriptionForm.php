<?php


namespace frontend\models;


use common\models\Rate;
use yii\base\Model;

class SubscriptionForm extends Model
{
    public $subscription_id;

    public function rules(): array
    {
        return [
            [['subscription_id'], 'required'],
            [['subscription_id'], 'integer'],
            ['subscription_id', 'exist', 'skipOnError' => true, 'targetClass' => Rate::class, 'targetAttribute' => ['subscription_id' => 'id']],
        ];
    }
}