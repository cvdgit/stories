<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\bootstrap\BootstrapAsset;
use yii\web\YiiAsset;
use yii\bootstrap\BootstrapPluginAsset;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style.css',
    ];
    public $js = [
        'js/wikids.js',
        'js/main.js',
    ];
    public $depends = [
        BootstrapAsset::class,
        YiiAsset::class,
        BootstrapPluginAsset::class,
    ];
}