<?php

use common\components\module\Provider;
use common\components\module\routes\RoutesLoader;

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
        'devicedetect',
        'crawlerdetect',
        Provider::class,
        RoutesLoader::class,
    ],
    'controllerNamespace' => 'frontend\controllers',
    'on beforeAction' => static function($event) {
        \common\models\User::updateLastActivity();
    },
    'modules' => [
        'repetition' => ['class' => \frontend\modules\repetition\Module::class],
        'learning-path' => ['class' => \frontend\modules\LearningPath\Module::class],
    ],
    'container' => [
        'definitions' => [
            \modules\edu\RepetitionApiInterface::class => \frontend\modules\repetition\RepetitionApiProvider::class,
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-wikids',
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'domain' => $params['cookieDomain'],
                'sameSite' => \yii\web\Cookie::SAME_SITE_NONE,
                'secure' => true,
            ],
            'loginUrl' => ['/auth/login'],
            'on beforeLogout' => static function() {
                $readCookies = Yii::$app->response->cookies;
                if ($readCookies->has('uid')) {
                    $readCookies->remove('uid');
                }
            },
        ],
        'session' => [
            'name' => 'wikids',
            'cookieParams' =>[
                'httpOnly' => true,
                'domain' => $params['cookieDomain'],
                'sameSite' => \yii\web\Cookie::SAME_SITE_NONE,
                'secure' => true,
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'notamedia\sentry\SentryTarget',
                    'dsn' => $params['sentry.dsn'],
                    'levels' => ['error', 'warning'],
                    'context' => true,
                ],
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
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['neo.*'],
                    'logFile' => '@runtime/logs/neo.log',
                    'logVars' => [],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => $urlManager,
        'urlManagerBackend' => $params['components.urlManagerBackend'],
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
                /*'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => $params['fbClientId'],
                    'clientSecret' => $params['fbClientSecret'],
                    'returnUrl' => 'https://wikids.ru/auth?authclient=facebook',
                ],*/
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => $params['vkClientId'],
                    'clientSecret' => $params['vkClientSecret'],
                    'scope' => 'email',
                    'returnUrl' => 'https://wikids.ru/auth?authclient=vkontakte',
                ],
                'yandex' => [
                    'class' => 'yii\authclient\clients\Yandex',
                    'clientId' => $params['yaClientId'],
                    'clientSecret' => $params['yaClientSecret'],
                    'returnUrl' => 'https://wikids.ru/auth?authclient=yandex',
                ],
            ],
        ],
        'queue' => $params['components.queue'],
        'devicedetect' => [
            'class' => 'alexandernst\devicedetect\DeviceDetect',
        ],
        'unisender' => [
            'class' => \common\components\unisender\UniSenderComponent::class,
            'apiConfig' => [
                'apiKey' => $params['unisenderKey'],
            ],
        ],
        'crawlerdetect' => [
            'class' => 'alikdex\crawlerdetect\CrawlerDetect',
            'setParams' => true, // optional, bootstrap initialize requred
        ],
    ],
    'params' => $params,
];
