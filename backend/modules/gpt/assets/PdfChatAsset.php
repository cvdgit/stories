<?php

declare(strict_types=1);

namespace backend\modules\gpt\assets;

use yii\web\AssetBundle;

class PdfChatAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '../build/pdf_chat.css',
    ];
    public $js = [
        '../build/pdf_chat.js',
    ];
    public $jsOptions = [
        'crossorigin' => 'anonymous',
    ];
}
