<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/responsive.css',
        'css/story.css',
        'css/owl.carousel.min.css',
        'css/lazyYT.min.css',
        'css/slick.css',
    ];
    public $js = [
        'js/main.js',
        'js/owl.carousel.min.js',
        'js/lazyYT.js',
        'js/masonry.pkgd.min.js',
        'js/slick.min.js',
        'js/jquery.counterup.min.js',
        'js/jquery.waypoints.min.js',
        'js/jquery.upload.preview.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'frontend\assets\FontAwesomeAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
