<?php

namespace backend\assets;

use yii\web\AssetBundle;

class WaveSurferAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        '/build/audio.js',
    ];
}