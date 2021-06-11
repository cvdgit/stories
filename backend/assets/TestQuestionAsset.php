<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class TestQuestionAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/test-question.js',
    ];
    public $depends = [
        YiiAsset::class,
        SvgAsset::class,
    ];
}
