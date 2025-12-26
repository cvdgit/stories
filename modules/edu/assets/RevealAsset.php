<?php

declare(strict_types=1);

namespace modules\edu\assets;

use yii\web\AssetBundle;

class RevealAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        '/build/reveal.js',
    ];
    public $css = [
        '/build/reveal.css',
    ];
}
