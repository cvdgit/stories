<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class SlidesAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/build/slides.css',
    ];

    public $js = [
        '/build/slides.js',
    ];

    public $depends = [
        AppAsset::class,
    ];
}
