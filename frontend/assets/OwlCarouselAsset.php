<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class OwlCarouselAsset extends AssetBundle
{
    public $sourcePath = '@bower/original-owl-carousel';
    public $css = [
        'dist/assets/owl.carousel.css',
    ];
    public $js = [
        'dist/owl.carousel.min.js',
    ];
    public $depends = [
        //'frontend\assets\AppAsset',
    ];
}