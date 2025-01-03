<?php
return [
    'adminEmail' => 'admin@wikids.ru',
    'supportEmail' => 'info@wikids.ru',
    'infoEmail' => 'info@wikids.ru',
    'contactEmail' => 'contact@wikids.ru',
    'user.passwordResetTokenExpire' => 3600,
    'user.rememberMeDuration' => 3600 * 24 * 30,
    'dropboxSlidesPath' => 'Приложения\Slides App\\',
    'coverFolder' => 'slides_cover',
    'storyImagesFolder' => 'slides',
    'storyFilesFolder' => 'slides_file',

    'components.queue' => [
        'class' => \yii\queue\db\Queue::class,
        'db' => 'db',
        'ttr' => 5 * 60,
        'attempts' => 3,
        'tableName' => '{{%queue}}',
        'mutex' => \yii\mutex\MysqlMutex::class,
        'channel' => 'default',
        'as log' => \yii\queue\LogBehavior::class,
    ],

    'components.urlManagerFrontend' => [
        'class' => 'yii\web\UrlManager',
        'baseUrl' => 'https://wikids.ru',
        'hostInfo' => 'https://wikids.ru',
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'enableStrictParsing' => true,
        'rules' => [
            '' => 'site/index',
            'story/<alias:[\w\d\-]+>' => 'story/view',
            'blog/<slug:[\w\d\-]+>' => 'news/view',
            'test/<id:\d+>' => 'test/view',
            'preview/<alias:[\w\-]+>' => 'preview/view',
            'study/task/<id:\d+>' => 'study/task',
            '<section:\w+>' => 'story/index',
            '/edu/story/<id:\d+>' => 'edu/story/view',
            '/learning-path/<id:[A-Za-z0-9_-]+>' => 'learning-path/default/index',
            '/video/player/<id>' => 'video/view',
        ],
    ],

    'test.question.images' => '/test_images/question',
    'slides.videos' => '/slides_video',

    'images.root' => [
        1 => '/admin/upload/',
        2 => '/slides/',
        3 => '/upload/testing/',
    ],

    'study.url' => 'https://study.wikids.ru',

    'folder.question_audio' => 'question_audio',
];
