<?php


namespace frontend\assets;


use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class LazyAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-lazy';

    public $js = [
        'jquery.lazy.min.js',
    ];

    public $depends = [
        JqueryAsset::class
    ];
}