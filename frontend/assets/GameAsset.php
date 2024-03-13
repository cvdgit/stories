<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class GameAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'game/Build/TestServerColor.loader.js',
    ];
}
