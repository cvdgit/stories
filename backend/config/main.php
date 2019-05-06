<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => [
        'log',
        'common\bootstrap\Bootstrap',
    ],
    'modules' => [],
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
            'loginUrl' => null,
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
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'showScriptName' => false,
        ],
        'urlManagerFrontend' => [
            'class' => 'yii\web\UrlManager',
            'baseUrl' => 'https://wikids.ru',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                '' => 'site/index',
                'story/<alias:[\w\d\-]+>' => 'story/view',
            ]
        ],
        'dropbox' => [
            'class' => 'creocoder\flysystem\DropboxFilesystem',
            'token' => 'DSo_oETVRJAAAAAAAAAAD3wNjPsT23MVVpNW5gyOXj5m8WaQ_bihi0ODas2bXgYe',
            'app' => 'cvd-slides-app2',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
    ],
    'params' => $params,
];
