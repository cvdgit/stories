<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class RevealAsset extends AssetBundle
{
    public $sourcePath = '@bower/reveal.js';
    public $css = [
        '/css/offline-v2.css',
        '/js/revealjs-customcontrols/customcontrols.css',
    ];
    public $js = [
        'js/reveal.js',
        'lib/js/head.min.js',
        '/js/story-reveal.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
