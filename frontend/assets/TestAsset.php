<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class TestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/wikids-reveal.css',
    ];
    public $js = [
        'js/wikids-story-test.js',
        'js/PatienceDiff.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        SortableJsAsset::class,
    ];
}
