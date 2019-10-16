<?php


namespace frontend\assets;


use yii\web\AssetBundle;

class PlyrAsset extends AssetBundle
{
    public $sourcePath = '@bower/plyr/dist';
    public $css = [
        'plyr.css',
    ];
    public $js = [
        'plyr.min.js',
    ];
    public $depends = [];
}
