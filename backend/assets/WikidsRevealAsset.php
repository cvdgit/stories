<?php


namespace backend\assets;


use yii\web\AssetBundle;

class WikidsRevealAsset extends AssetBundle
{
    public $basePath = '@webroot';
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