<?php

namespace backend\assets;

use common\assets\panzoom\PanzoomAsset;
use yii\web\AssetBundle;

class TestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '../build/story_quiz.css',
    ];
    public $js = [
        '../js/main.js',
        '../build/story_quiz.js',
    ];
    public $depends = [
        PanzoomAsset::class,
    ];
}
