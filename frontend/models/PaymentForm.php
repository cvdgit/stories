<?php


namespace frontend\models;


use yii\base\Model;

class PaymentForm extends Model
{
    public $terminalkey;
    public $frame = 'false';
    public $language = 'ru';
    public $amount;
    public $order;
    public $description;
    public $name;
    public $email;
    public $phone;

    public function formName()
    {
        return '';
    }

}