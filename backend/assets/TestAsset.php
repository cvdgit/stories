<?php

namespace backend\assets;

use frontend\assets\SortableJsAsset;
use yii\web\AssetBundle;

class TestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    //public $baseUrl = '/';
    public $baseUrl = '@web';
    public $css = [
        //'css/wikids-reveal.css',
        '/build/story_quiz.css',
    ];
    public $js = [
        '/js/main.js',
        '/build/story_quiz.js',
        //'js/wikids-story-test.js',
        //'js/PatienceDiff.js',
    ];
    public $depends = [
        AppAsset::class,
        //SortableJsAsset::class,
        //MaphilightAsset::class,
    ];
}
