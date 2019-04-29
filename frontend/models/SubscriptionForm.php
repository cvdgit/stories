<?php


namespace frontend\models;


use common\models\Rate;
use DateInterval;
use DateTime;
use yii\base\Model;

class SubscriptionForm extends Model
{

    public $subscription_id;
    public $start_date;
    public $finish_date;

    private $_rate;

    public function rules(): array
    {
        return [
            [['subscription_id'], 'required'],
            [['subscription_id'], 'integer'],
            ['subscription_id', 'exist', 'skipOnError' => true, 'targetClass' => Rate::class, 'targetAttribute' => ['subscription_id' => 'id']],
            ['subscription_id', 'isValid'],
        ];
    }

    /**
     * @param string $attribute the attribute currently being validated
     * @param mixed $params the value of the "params" given in the rule
     * @param \yii\validators\InlineValidator $validator related InlineValidator instance.
     */
    public function isValid($attribute, $params, $validator)
    {
        $rate = $this->getRate();
        if ($rate->isArchive()) {
            $this->addError($attribute, 'Невозможно создать платеж т.к. подписка находится в архиве');
        }
    }

    public function afterValidate()
    {
        parent::afterValidate();
        $this->start_date = date('Y-m-d H:i:s');
        $this->finish_date = $this->calculateFinishDate();
    }

    protected function calculateFinishDate()
    {
        $rate = $this->getRate();
        $date = new DateTime($this->start_date);
        return $date->add(new DateInterval("P{$rate->days}D"))->format('Y-m-d H:i:s');
    }

    public function getRate()
    {
        if ($this->_rate === null) {
            $this->_rate = Rate::findOne($this->subscription_id);
        }
        return $this->_rate;
    }

}