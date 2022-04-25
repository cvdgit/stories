<?php

namespace backend\assets;

use yii\web\AssetBundle;

class CourseAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        '../build/course.js',
    ];
    public $css = [
        '../build/course.css',
    ];
    public $depends = [
        AppAsset::class,
    ];
}
