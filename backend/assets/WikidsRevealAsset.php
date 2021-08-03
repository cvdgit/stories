<?php

namespace backend\assets;

use yii\web\AssetBundle;
use common\assets\RevealAsset;

class WikidsRevealAsset extends AssetBundle
{
    public $basePath = '@public';
    public $baseUrl = '/';
    public $css = [
        'css/wikids-reveal.css',
    ];
    public $js = [
        'js/player/player.js',
    ];
    public $depends = [
        RevealAsset::class
    ];
}
