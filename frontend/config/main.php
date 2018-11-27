<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                '' => 'site/index',
                'contacts' => 'site/contact',
                'pricing' => 'rate/index', //'site/pricing',
                'captcha' => 'site/captcha',
                'signup' => 'site/signup',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'request-password-reset' => 'site/request-password-reset',
                'signup-confirm' => 'site/signup-confirm',
                'profile' => 'profile/index',
                
                'stories/tag/<tag:[\w]+>' => 'story/tag',
                'stories/category/<category:[\w]+>' => 'story/category',
                'stories' => 'story/index',

                'story/webhook' => 'story/webhook',
                'story/<alias:[\w\-]+>' => 'story/view',
                'story/viewbyframe/<id:\d+>' => 'story/view-by-frame',
                'success' => 'rate/success',
                'fail' => 'rate/fail',
            ],
        ],
    ],
    'params' => $params,
];
