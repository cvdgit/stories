<?php

declare(strict_types=1);

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\jui\JuiAsset;

class ContextMenuAsset extends AssetBundle
{
    public $sourcePath = '@backend/assets/contextmenu';
    public $js = [
        'jquery.ui-contextmenu.min.js',
    ];
    public $depends = [
        JqueryAsset::class,
        JuiAsset::class,
    ];
}
