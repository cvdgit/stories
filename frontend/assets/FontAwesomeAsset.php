<?php
namespace frontend\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@bower/font-awesome';

    public $css = [
        'web-fonts-with-css/css/fontawesome-all.min.css',
    ];
}
