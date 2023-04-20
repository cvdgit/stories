<?php

use backend\modules\repetition\query\ScheduleFetcher;
use backend\modules\repetition\ScheduleFetcherInterface;

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
    'modules' => [
        'repetition' => \backend\modules\repetition\Module::class,
        'changelog' => \backend\modules\changelog\Module::class,
    ],
    'container' => [
        'definitions' => [
            ScheduleFetcherInterface::class => ScheduleFetcher::class,
            \backend\modules\repetition\RepetitionApiInterface::class => \frontend\modules\repetition\RepetitionApiProvider::class,
            \backend\Changelog\ChangelogApiInterface::class => \backend\modules\changelog\ChangelogApi::class,
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-wikids',
            'cookieValidationKey' => $params['cookieValidationKey'],
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
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
                    'class' => 'notamedia\sentry\SentryTarget',
                    'dsn' => $params['sentry.dsn'],
                    'levels' => ['error', 'warning'],
                    'context' => true,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'except' =>
                        [
                            'yii\web\HttpException:404',
                            'yii\web\HttpException:403',
                            'yii\validators\FileValidator::getSizeLimit'
                        ],
                    'levels' => ['error', 'warning'],
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
                    'categories' => ['yii\web\HttpException:403'],
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/403.log',
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'showScriptName' => false,
        ],
        'urlManagerFrontend' => $params['components.urlManagerFrontend'],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'unisender' => [
            'class' => \common\components\unisender\UniSenderComponent::class,
            'apiConfig' => [
                'apiKey' => $params['unisenderKey'],
            ],
        ],
        'queue' => $params['components.queue'],
    ],
    'params' => $params,
];
