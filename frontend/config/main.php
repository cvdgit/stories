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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                '' => 'site/index',
                'contacts' => 'site/contact',
                'captcha' => 'site/captcha',
                'policy' => 'site/policy',
                'request-password-reset' => 'site/request-password-reset',

                'pricing' => 'rate/index',

                'login' => 'auth/login',
                'auth' => 'auth/auth',
                'logout' => 'auth/logout',

                'signup' => 'signup/request',
                'signup-confirm' => 'signup/signup-confirm',

                // 'reset-password' => 'site/reset-password',

                'payment' => 'payment/create',
                'payment/notify' => 'payment/notify',

                'profile' => 'profile/index',
                'change-password' => 'profile/change-password',
                
                'stories/tag/<tag:[\w]+>' => 'story/tag',
                'stories/category/<category:[\w\-]+>' => 'story/category',
                'stories' => 'story/index',

                'story/webhook' => 'story/webhook',
                'story/addcomment' => 'story/add-comment',
                'story/<alias:[\w\-]+>' => 'story/view',

                'success' => 'rate/success',
                'fail' => 'rate/fail',
                'file-avatar' => 'upload/file-avatar',

                'statistics/write/<id:\d+>' => 'statistics/write',
                'feedback/create/<id:\d+>' => 'feedback/create',
            ],
        ],
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
