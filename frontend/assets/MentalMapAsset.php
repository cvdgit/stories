<?php

declare(strict_types=1);

namespace frontend\assets;

use yii\web\AssetBundle;

class MentalMapAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        '/build/mental_map_quiz.css',
    ];

    public $js = [
        '/build/mental_map_quiz.js',
    ];

    public $depends = [
        AppAsset::class,
    ];
}
