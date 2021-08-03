<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class MaphilightAsset extends AssetBundle
{
    public $sourcePath = '@bower/maphilight';
    public $js = [
        'jquery.maphilight.js',
    ];
    public $depends = [
        JqueryAsset::class
    ];
}
