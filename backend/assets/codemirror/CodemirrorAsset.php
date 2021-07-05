<?php

namespace backend\assets\codemirror;

use yii\web\AssetBundle;

class CodemirrorAsset extends AssetBundle
{

    public $sourcePath = '@backend/assets/codemirror';

    public $js = [
        'formatting.js',
    ];

    public $depends = [
        \conquer\codemirror\CodemirrorAsset::class,
    ];

}