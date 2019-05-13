<?php

namespace backend\assets;

use yii\web\AssetBundle;

class RevealAsset extends AssetBundle
{
    public $sourcePath = '@bower/reveal.js';
    public $css = [
        //'css/reveal.css',
    ];
    public $js = [
        'js/reveal.js',
    ];
    public $depends = [
        AppAsset::class,
    ];
}
