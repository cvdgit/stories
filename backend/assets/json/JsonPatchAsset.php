<?php

declare(strict_types=1);

namespace backend\assets\json;

use yii\web\AssetBundle;
use yii\web\View;

class JsonPatchAsset extends AssetBundle
{
    public $sourcePath = '@backend/assets/json';
    public $js = [
        'fast-json-patch.min.js',
    ];
}
