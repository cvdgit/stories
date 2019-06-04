<?php
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
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
        ],
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
            ],
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
            'class' => \matperez\yii2unisender\UniSender::class,
            'apiConfig' => [
                'apiKey' => $params['unisenderKey'],
            ],
        ],
        'queue' => $params['components.queue'],
    ],
    'params' => $params,
];
