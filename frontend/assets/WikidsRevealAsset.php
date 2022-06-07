<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class WikidsRevealAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/wikids-reveal.css',
        '/build/story_quiz.css',
    ];
    public $js = [
        'js/player/player.js',
        /*'js/wikids-story-test.js',
        'js/PatienceDiff.js',*/
        '/build/story_quiz.js',
    ];
    public $depends = [
        FrontendRevealAsset::class
    ];
}
