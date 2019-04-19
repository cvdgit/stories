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
                'pricing' => 'rate/index',
                'captcha' => 'site/captcha',
                'signup' => 'site/signup',
                'login' => 'site/login',
                'auth' => 'site/auth',
                'logout' => 'site/logout',
                'policy' => 'site/policy',
                'request-password-reset' => 'site/request-password-reset',
                'signup-confirm' => 'site/signup-confirm',
                // 'reset-password' => 'site/reset-password',

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
                    'clientId' => 'google_client_id',
                    'clientSecret' => 'google_client_secret',
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => 'facebook_client_id',
                    'clientSecret' => 'facebook_client_secret',
                ],
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => '6952047',
                    'clientSecret' => '5v4urGJMSy3BF992dIVd',
                    'scope' => 'email',
                    'returnUrl' => 'https://wikids.ru/auth',
                ],
            ],
        ],
    ],
    'params' => $params,
];
