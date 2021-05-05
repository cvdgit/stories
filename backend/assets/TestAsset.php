<?php

namespace backend\assets;

use frontend\assets\SortableJsAsset;
use yii\web\AssetBundle;

class TestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '/';
    public $css = [
        'css/wikids-reveal.css',
    ];
    public $js = [
        'js/main.js',
        'js/wikids-story-test.js',
        'js/PatienceDiff.js',
    ];
    public $depends = [
        AppAsset::class,
        'yii\web\YiiAsset',
        SortableJsAsset::class,
    ];
}