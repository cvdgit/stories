<?php

namespace backend\assets;

use yii\web\AssetBundle;

class RecorderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '/';
    public $js = [
        'js/recorder.js',
    ];
}