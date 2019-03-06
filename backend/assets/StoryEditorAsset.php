<?php

namespace backend\assets;

use yii\web\AssetBundle;

class StoryEditorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        'js/story-editor.js',
    ];
    public $depends = [
        'backend\assets\RevealAsset',
    ];
}
