<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class RevealAsset extends AssetBundle
{
    public $sourcePath = '@bower/reveal.js';
    public $css = [
        //'css/reveal.css',
    ];
    public $js = [
        'dist/reveal.js',
    ];
    public $depends = [
        AppAsset::class,
    ];
}
