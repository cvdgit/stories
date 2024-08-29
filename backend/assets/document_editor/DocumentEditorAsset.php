<?php

declare(strict_types=1);

namespace backend\assets\document_editor;

use yii\web\AssetBundle;

class DocumentEditorAsset extends AssetBundle
{
    public $sourcePath = '@backend/assets/document_editor';
    public $js = [
        'document_editor.js',
    ];
    public $css = [
        'document_editor.css',
    ];
}
