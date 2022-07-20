<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class TestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/build/story_quiz.css',
    ];

    public $js = [
        '/build/story_quiz.js',
    ];

    public $depends = [
        AppAsset::class,
    ];
}
