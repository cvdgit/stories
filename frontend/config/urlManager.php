<?php
return [
    'baseUrl' => '/',
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '' => 'site/index',

        'contacts' => 'site/contact',
        'captcha' => 'site/captcha',
        'policy' => 'site/policy',
        'copyright' => 'site/copyright',
        'request-password-reset' => 'site/request-password-reset',

        'pricing' => 'rate/index',

        'login' => 'auth/login',
        'auth' => 'auth/auth',
        'logout' => 'auth/logout',

        'signup' => 'signup/request',
        'signup-confirm' => 'signup/signup-confirm',

        'reset-password' => 'site/reset-password',

        'payment' => 'payment/create',
        'payment/notify' => 'payment/notify',

        'profile' => 'profile/index',
        'profile/edit' => 'profile/update',
        'change-password' => 'profile/change-password',

        'stories/history' => 'story/history',
        'stories/liked' => 'story/liked',

        'stories/favorites' => 'story/favorites',
        'favorites/add/<story_id:\d+>' => 'story/add-favorites',

        'stories/skazki-na-noch' => 'story/bedtime-stories',
        'stories/audio' => 'story/audio-stories',

        'stories/tag/<tag:[\w\s\-]+>' => 'story/tag',
        'stories/category/<category:[\w\-]+>' => 'story/category',
        'stories' => 'story/index',

        'story/random' => 'story/random',
        'story/like' => 'story/like',
        'story/addcomment' => 'story/add-comment',
        'story/get-story-body/<id:\d+>' => 'story/get-story-body',
        'story/get-story-test/<id:\d+>' => 'story/get-story-test',
        'story/store-test-result' => 'story/store-test-result',
        'story/init-story-player' => 'story/init-story-player',
        'story/myaudio' => 'story/myaudio',
        'story/<alias:[\w\-]+>/<list:\d+>' => 'story/view',
        'story/<alias:[\w\-]+>' => 'story/view',

        'success' => 'rate/success',
        'fail' => 'rate/fail',
        'file-avatar' => 'upload/file-avatar',

        'statistics/write/<id:\d+>' => 'statistics/write',
        'feedback/create/<id:\d+>' => 'feedback/create',

        'blog' => 'news/index',
        'blog/<slug:[\w\-]+>' => 'news/view',

        'player/create-audio-track' => 'player/create-audio-track',

        'comment/reply/<id:\d+>' => 'comment/reply',

        'image/<id:[\w\d]+' => 'image/view',
    ],
];