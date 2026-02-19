<?php

declare(strict_types=1);

namespace common\assets;

use yii\web\AssetBundle;

class StoryPluginsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        '/build/storyPlugins.js',
    ];
    public $css = [
        '/build/storyPlugins.css',
    ];
}
