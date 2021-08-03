<?php

namespace backend\assets;

use frontend\assets\PlyrAsset;
use yii\jui\JuiAsset;
use yii\web\AssetBundle;

class StoryEditorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/story-editor.js',
        'js/story-image.js',
    ];
    public $css = [
        'css/editor.css',
    ];
    public $depends = [
        AppAsset::class,
        JuiAsset::class,
        PlyrAsset::class,
        CKEditorAsset::class,
    ];
}
