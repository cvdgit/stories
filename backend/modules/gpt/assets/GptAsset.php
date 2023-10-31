<?php

declare(strict_types=1);

namespace backend\modules\gpt\assets;

use yii\web\AssetBundle;

class GptAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '../build/wikids_gpt.css',
    ];
    public $js = [
        '../build/wikids_gpt.js',
    ];
    public $jsOptions = [
        'crossorigin' => 'anonymous',
    ];
}
