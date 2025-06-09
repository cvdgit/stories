<?php

namespace backend\assets;

use yii\web\AssetBundle;

class MathAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/build/math.js',
    ];
}
