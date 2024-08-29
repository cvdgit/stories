<?php

declare(strict_types=1);

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\jui\JuiAsset;

class FancytreeAsset extends AssetBundle
{
    public $sourcePath = '@bower/fancytree';
    public $skin = 'dist/skin-bootstrap/ui.fancytree';

    /**
     * @inheritdoc
     */
    public $depends = [
        JqueryAsset::class,
        JuiAsset::class
    ];

    /**
     * Set up CSS and JS asset arrays based on the base-file names
     * @param string $type whether 'css' or 'js'
     * @param array $files the list of 'css' or 'js' basefile names
     */
    protected function setupAssets($type, $files = [])
    {
        $srcFiles = [];
        $minFiles = [];
        foreach ($files as $file) {
            $srcFiles[] = "{$file}.{$type}";
            $minFiles[] = "{$file}.min.{$type}";
        }
        if (empty($this->$type)) {
            $this->$type = YII_DEBUG ? $srcFiles : $minFiles;
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setupAssets('css', [$this->skin]);
        $this->setupAssets('js', ['dist/jquery.fancytree-all']);
        parent::init();
    }
}
