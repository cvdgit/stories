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

    public static function homeUrl()
    {
        return 'https://' . Yii::$app->request->serverName;
    }

    public static function getServerUrl(): string
    {
        $serverName = $_SERVER['SERVER_NAME'];
        if (!in_array($_SERVER['SERVER_PORT'], [80, 443])) {
            $port = ":$_SERVER[SERVER_PORT]";
        } else {
            $port = '';
        }
        if (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }
        return $scheme . '://' . $serverName . $port;
    }
}
