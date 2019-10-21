<?php

namespace backend\assets;

use yii\jui\JuiAsset;
use yii\web\AssetBundle;

class RevealAsset extends AssetBundle
{
    public $sourcePath = '@bower/reveal.js';
    public $css = [
    ];
    public $js = [
        'js/reveal.js',
    ];
    public $depends = [
        AppAsset::class,
        JuiAsset::class,
    ];
}
