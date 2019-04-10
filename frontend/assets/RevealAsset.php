<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class RevealAsset extends AssetBundle
{
    public $sourcePath = '@bower/reveal.js';
    public $css = [
        'css/reveal.css',
        '/js/revealjs-customcontrols/customcontrols.css',
    ];
    public $js = [
        'js/reveal.js',
    ];
}
