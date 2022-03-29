<?php
return [
    'language' => 'ru',
    'sourceLanguage' => 'en',
    'name' => 'Wikids',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
         'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy HH:mm:ss',
            'timeFormat' => 'HH:mm:ss',
            'locale' => 'ru-RU',
            'defaultTimeZone' => 'Europe/Moscow',
            'timeZone' => 'MSK',
        ],
    ],
];
