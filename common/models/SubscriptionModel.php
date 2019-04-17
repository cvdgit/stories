<?php

namespace common\models;

use DateInterval;
use DateTime;
use yii\base\Model;

class SubscriptionModel extends Model
{

    public const SCENARIO_FRONTEND = 'frontend';
    public const SCENARIO_BACKEND = 'backend';

	public $subscription_id;
	public $date_start;
	public $date_finish;
    public $state;

    public function rules(): array
    {
        return [
            [['subscription_id'], 'required'],
            [['subscription_id'], 'integer'],
            ['subscription_id', 'exist', 'skipOnError' => true, 'targetClass' => Rate::class, 'targetAttribute' => ['subscription_id' => 'id']],
            [['date_start', 'date_finish'], 'date', 'format' => 'yyyy.MM.dd'],
            ['state', 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return [
            self::SCENARIO_FRONTEND => ['subscription_id'],
            self::SCENARIO_BACKEND => ['subscription_id', 'date_start', 'date_finish'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
        	'subscription_id' => 'Подписка',
            'date_start' => 'Дата начала подписки',
            'date_finish' => 'Дата окончания подписки',
            'state' => 'Статус',
        ];
    }

    public function calculateSubscriptionDates($monthCount): void
    {
        $this->date_start = date('Y-m-d H:i:s');
        $this->date_finish = $this->getSubscriptionFinishDate($monthCount);
        $this->state = Payment::STATUS_VALID;
    }

    protected function getSubscriptionFinishDate($monthCount): string
    {
        $days = $monthCount * 30;
        $date = new DateTime($this->date_start);
        return $date->add(new DateInterval("P{$days}D"))->format('Y-m-d H:i:s');
    }

}
