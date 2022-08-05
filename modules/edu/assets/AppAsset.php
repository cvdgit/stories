<?php

declare(strict_types=1);


namespace modules\edu\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;
use yii\bootstrap\BootstrapAsset;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@modules/edu/assets';
    public $css = [
        'css/edu.css',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];
    public $publishOptions = [
        'forceCopy'=>true,
    ];
}
