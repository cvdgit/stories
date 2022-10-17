<?php

use modules\edu\components\StudentContext;
use yii\mail\MailerInterface;
use yii\rbac\DbManager;
use yii\caching\FileCache;
use yii\swiftmailer\Mailer;

return [
    'language' => 'ru',
    'sourceLanguage' => 'en',
    'timeZone' => 'Europe/Moscow',
    'name' => 'Wikids',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'bootstrap' => [

    ],
    'modules' => [
        'files' => ['class' => \modules\files\Module::class],
        'edu' => ['class' => \modules\edu\Module::class],
    ],
    'components' => [
        'studentContext' => [
            'class' => StudentContext::class,
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'cacheFrontend' => [
            'class' => FileCache::class,
            'cachePath' => Yii::getAlias('@frontend') . '/runtime/cache',
        ],
        'cacheBackend' => [
            'class' => FileCache::class,
            'cachePath' => Yii::getAlias('@backend') . '/runtime/cache',
        ],
        'authManager' => [
            'class' => DbManager::class,
            'cache' => 'cache',
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy HH:mm:ss',
            'timeFormat' => 'HH:mm:ss',
            'locale' => 'ru-RU',
            'defaultTimeZone' => 'Europe/Moscow',
        ],
    ],
];
