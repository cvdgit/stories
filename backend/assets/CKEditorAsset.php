<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class CKEditorAsset extends AssetBundle
{
    public $sourcePath = '@bower/ckeditor';
    public $js = [
        'ckeditor.js',
        'adapters/jquery.js',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}