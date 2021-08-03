<?php

namespace common\assets;

use yii\web\AssetBundle;

class RevealAsset extends AssetBundle
{
    public $sourcePath = '@bower/reveal.js';
    public $css = [
        'dist/reveal.css',
    ];
    public $js = [
        'dist/reveal.js',
    ];
}
