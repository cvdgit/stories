<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\bootstrap\BootstrapPluginAsset;

/**
 * Main frontend application asset bundle.
 */
class GameAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [];
    public $depends = [
        YiiAsset::class,
        BootstrapPluginAsset::class,
    ];
}
