<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$urlManager = require __DIR__ . '/urlManager.php';

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'common\bootstrap\Bootstrap',
    ],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-wikids',
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'domain' => $params['cookieDomain'],
            ],
            'loginUrl' => ['/'],
        ],
        'session' => [
            'name' => 'wikids',
            'cookieParams' =>[
                'httpOnly' => true,
                'domain' => $params['cookieDomain'],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['yii\web\HttpException:404'],
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/404.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'except' => ['yii\web\HttpException:404'],
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['payment_fail'],
                    'logFile' => '@runtime/logs/pay.log',
                    'logVars' => [],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => $urlManager,
        'view' => [
            'as seo' => [
                'class' => 'frontend\components\SeoViewBehavior',
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => $params['googleClientId'],
                    'clientSecret' => $params['googleClientSecret'],
                    'returnUrl' => 'https://wikids.ru/auth?authclient=google',
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => $params['fbClientId'],
                    'clientSecret' => $params['fbClientSecret'],
                    'returnUrl' => 'https://wikids.ru/auth?authclient=facebook',
                ],
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => $params['vkClientId'],
                    'clientSecret' => $params['vkClientSecret'],
                    'scope' => 'email',
                    'returnUrl' => 'https://wikids.ru/auth?authclient=vkontakte',
                ],
            ],
        ],
    ],
    'params' => $params,
];
