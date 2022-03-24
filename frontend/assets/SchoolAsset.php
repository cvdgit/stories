<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class SchoolAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'build/school/app.css',
    ];
    public $js = [
        'build/school/app.js',
    ];
    public $depends = [
        YiiAsset::class,
    ];
}