<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class RevealAsset extends AssetBundle
{
    public $sourcePath = '@bower/reveal.js';
    public $css = [
        //'css/reveal.css',
        //'css/simple.css',
        //'css/lib/css/zenburn.css',
    ];
    public $js = [
        'js/reveal.js',
        'lib/js/head.min.js',
    ];
    public $depends = [
        //'frontend\assets\AppAsset',
    ];
}