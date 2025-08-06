<?php

namespace backend\assets;

use yii\web\AssetBundle;

class MathAsset extends AssetBundle
{
    public $basePath = '@public';
    public $baseUrl = '@web';

    public $js = [
        '/build/math.js',
    ];

    public $css = [
        '/build/math.css',
    ];
}
