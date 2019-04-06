<?php

namespace common\helpers;

use Yii;

class Url extends \yii\helpers\Url
{
    public static function isHome()
    {
        return (self::home() == Yii::$app->request->url);
    }
}
