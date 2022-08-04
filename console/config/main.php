<?php

use yii\console\controllers\FixtureController;
use yii\console\controllers\MigrateController;
use yii\console\controllers\ServeController;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$frontendUrlManager = require __DIR__ . '/../../frontend/config/urlManager.php';

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'common\bootstrap\Bootstrap',
        'queue',
    ],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => FixtureController::class,
            'namespace' => 'common\fixtures',
        ],
        'migrate' => [
            'class' => MigrateController::class,
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
                //'modules\edu\migrations',
                //'modules\files\migrations',
            ],
        ],
        'serve' => [
            'class' => ServeController::class,
            'docroot' => '@public',
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'notamedia\sentry\SentryTarget',
                    'dsn' => $params['sentry.dsn'],
                    'levels' => ['error', 'warning'],
                    'context' => true,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => array_merge($frontendUrlManager, [
            'hostInfo' => 'https://wikids.ru',
        ]),
        'urlManagerFrontend' => $params['components.urlManagerFrontend'],
        'unisender' => [
            'class' => \common\components\unisender\UniSenderComponent::class,
            'apiConfig' => [
                'apiKey' => $params['unisenderKey'],
                'retryCount' => 3,
            ],
        ],
        'queue' => $params['components.queue'],
    ],
    'params' => $params,
];
