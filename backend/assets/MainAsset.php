<?php

declare(strict_types=1);

namespace backend\assets;

use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $basePath = '@public';
    public $baseUrl = '@web';
    public $js = [
        '/build/main.js',
    ];
}
