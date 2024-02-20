<?php

declare(strict_types=1);

namespace backend\assets;

use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        '/build/main.js',
    ];
}
