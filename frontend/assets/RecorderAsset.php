<?php


namespace frontend\assets;


use yii\web\AssetBundle;

class RecorderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'js/recorder.js',
        // 'js/recorder-app.js',
    ];
}