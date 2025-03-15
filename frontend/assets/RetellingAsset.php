<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class RetellingAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/build/retelling_quiz.css',
    ];

    public $js = [
        '/build/retelling_quiz.js',
    ];

    public $depends = [
        AppAsset::class,
    ];
}
