<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class WikidsRevealAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/wikids-reveal.css',
    ];
    public $js = [
        'js/player/player.js',
        'js/wikids-story-test.js',
        'js/PatienceDiff.js',
    ];
    public $depends = [
        FrontendRevealAsset::class
    ];
}
