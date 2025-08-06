<?php

namespace backend\assets;

use yii\web\AssetBundle;

class MentalMapAsset extends AssetBundle
{
    public $basePath = '@public';
    public $baseUrl = '@web';

    public $js = [
        '/build/mental_map.js',
    ];

    public $css = [
        '/build/mental_map.css',
    ];
}
