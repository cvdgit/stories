<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class VideoAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'build/video.css',
    ];
    public $js = [
        'build/video.js',
    ];
}
