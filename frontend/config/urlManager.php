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
        'request-password-reset' => 'site/request-password-reset',

        'pricing' => 'rate/index',

        'login' => 'auth/login',
        'auth' => 'auth/auth',
        'logout' => 'auth/logout',

        'signup' => 'signup/request',
        'signup-confirm' => 'signup/signup-confirm',

        // 'reset-password' => 'site/reset-password',

        'payment' => 'payment/create',
        'payment/notify' => 'payment/notify',

        'profile' => 'profile/index',
        'profile/edit' => 'profile/update',
        'change-password' => 'profile/change-password',

        'storyes/history' => 'story/history',
        'stories/tag/<tag:[\w\s\-]+>' => 'story/tag',
        'stories/category/<category:[\w\-]+>' => 'story/category',
        'stories' => 'story/index',

        'story/webhook' => 'story/webhook',
        'story/addcomment' => 'story/add-comment',
        'story/get-story-body/<id:\d+>' => 'story/get-story-body',
        'story/<alias:[\w\-]+>' => 'story/view',

        'success' => 'rate/success',
        'fail' => 'rate/fail',
        'file-avatar' => 'upload/file-avatar',

        'statistics/write/<id:\d+>' => 'statistics/write',
        'feedback/create/<id:\d+>' => 'feedback/create',
    ],
];