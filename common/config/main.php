<?php
return [
    'language' => 'ru',
    'sourceLanguage' => 'en',
    'name' => 'Истории',
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
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin', 'author'],
            'itemFile' => '@common/rbac/items.php',
            'assignmentFile' => '@common/rbac/assignments.php',
            'ruleFile' => '@common/rbac/rules.php'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                 'class' => 'Swift_SmtpTransport',
                 'host' => 'smtp.yandex.ru',  // e.g. smtp.mandrillapp.com or smtp.gmail.com
                 'username' => 'info@wikids.ru',
                 'password' => 'Directum2019',
                 'port' => '587', // Port 25 is a very common port too
                 'encryption' => 'tls',
             ],
        ],
        //'assetManager' => [
        //    'bundles' => [
        //        'yii\web\JqueryAsset' => [
        //            'js' => [YII_DEBUG ? 'https://code.jquery.com/jquery-2.1.0.js' : 'https://code.jquery.com/jquery-2.1.0.min.js'],
        //            'jsOptions' => ['type' => 'text/javascript'],
        //        ],
        //    ],
        //],
    ],
];
