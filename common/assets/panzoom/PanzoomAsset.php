<?php

namespace common\assets\panzoom;

use yii\web\AssetBundle;

class PanzoomAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/panzoom';
    public $js = [
        'panzoom.min.js',
    ];
}
