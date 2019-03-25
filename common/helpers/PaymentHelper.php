<?php

namespace common\helpers;

use common\models\Payment;

class PaymentHelper
{

	public static function getStatusArray()
    {
        return [
            Payment::STATUS_NEW => 'Новый',
            Payment::STATUS_VALID => 'Активен',
            Payment::STATUS_INVALID => 'Отменен',
        ];
    }
    
    public static function getStatusText($status)
    {
        $arr = static::getStatusArray();
        return isset($arr[$status]) ? $arr[$status] : '';
    }

}