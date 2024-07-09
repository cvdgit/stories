<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class NewSchoolAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'build/new_school.css',
    ];
    public $js = [
        'build/new_school.js',
    ];
    public $depends = [
        YiiAsset::class,
    ];
}
