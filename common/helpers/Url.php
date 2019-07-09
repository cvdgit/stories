<?php

namespace common\helpers;

use Yii;

class Url extends \yii\helpers\Url
{
    public static function isHome(): bool
    {
        $controller = Yii::$app->controller;
        $default_controller = Yii::$app->defaultRoute;
        return (($controller->id === $default_controller) && ($controller->action->id === $controller->defaultAction));
    }
}
