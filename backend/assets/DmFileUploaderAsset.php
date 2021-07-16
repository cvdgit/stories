<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class DmFileUploaderAsset extends AssetBundle
{
    public $sourcePath = '@bower/dm-file-uploader/dist';
    public $css = ['css/jquery.dm-uploader.min.css'];
    public $js = ['js/jquery.dm-uploader.min.js'];
    public $depends = [
        JqueryAsset::class,
    ];
}
