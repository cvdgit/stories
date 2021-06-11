<?php

namespace backend\assets;

use yii\web\AssetBundle;

class SvgAsset extends AssetBundle
{

    public $sourcePath = '@backend/assets/svg';
    public $js = [
        'svg.js',
        'svg.draw.js',
        'svg.select.js',
        'svg.resize.js',
    ];
    public $css = [
        'svg.select.css',
    ];

}