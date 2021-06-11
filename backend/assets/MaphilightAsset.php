<?php

namespace backend\assets;

use yii\web\AssetBundle;

class MaphilightAsset extends AssetBundle
{

    public $sourcePath = '@bower/maphilight';
    public $js = [
        'jquery.maphilight.js',
    ];

}