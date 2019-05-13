<?php

namespace backend\assets;

use yii\web\AssetBundle;

class StoryEditorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/story-editor.js',
    ];
    public $depends = [
        RevealAsset::class,
        WikidsRevealAsset::class,
    ];
}
