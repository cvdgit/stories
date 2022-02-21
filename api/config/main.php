<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        common\bootstrap\Bootstrap::class,
    ],
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module'
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/course',
                    'tokens' => [
                        '{id}' => '<id:\w+>'
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/story',
                    'tokens' => [
                        '{id}' => '<id:\w+>'
                    ],
                    'only' => ['index', 'view'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/slide',
                    'tokens' => [
                        '{id}' => '<id:\w+>'
                    ],
                    'only' => ['view'],
                ],
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => 'v1/story-list',
                    'tokens' => [
                        '{id}' => '<id:\w+>'
                    ],
                    'only' => ['index'],
                ],
            ]
        ]
    ],
    'params' => $params,
];