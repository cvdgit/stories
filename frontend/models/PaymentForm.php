<?php


namespace frontend\models;


use common\models\Rate;
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
    public $receipt;

    public function formName()
    {
        return '';
    }

    public function makeReceipt(Rate $rate)
    {
        return [
            'Items' => [
                [
                    'Name' => $rate->title,
                    'Price' => $rate->cost * 100,
                    'Quantity' => 1,
                    'Amount' => $rate->cost * 100,
                    'Tax' => 'vat20',
                ],
            ],
            'Taxation' => 'usn_income',
        ];
    }

}