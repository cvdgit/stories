<?php

declare(strict_types=1);

namespace backend\assets;

use yii\web\AssetBundle;

class StoryCreateAssistAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/build/storyCreateAssist.css',
    ];
    public $js = [
        '/build/storyCreateAssist.js',
    ];
    public $jsOptions = [
        'crossorigin' => 'anonymous',
    ];
}
