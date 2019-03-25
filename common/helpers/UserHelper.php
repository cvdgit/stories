<?php

namespace common\helpers;

use yii\helpers\ArrayHelper;
use common\models\User;

class UserHelper
{

    public static function getUserArray()
    {
        return ArrayHelper::map(User::find()->all(), 'id', 'username');
    }

    public static function getStatusArray()
    {
        return [
            User::STATUS_DELETED => 'Удален',
            User::STATUS_WAIT => 'Ожидание подтверждения',
            User::STATUS_ACTIVE => 'Активен',
        ];
    }

    public static function getStatusText($status)
    {
        $arr = static::getStatusArray();
        return isset($arr[$status]) ? $arr[$status] : '';
    }

}
