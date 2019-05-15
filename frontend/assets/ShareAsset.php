<?php


namespace frontend\assets;


use yii\web\AssetBundle;

class ShareAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/share.js',
    ];
}