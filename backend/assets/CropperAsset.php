<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class CropperAsset extends AssetBundle
{
    public $sourcePath = '@bower/cropperjs/dist';
    public $css = [
        'cropper.css',
    ];
    public $js = [
        'cropper.js',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}
