<?php


namespace backend\assets;


use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class CropperAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/cropperjs/dist';

    /**
     * @inheritdoc
     */
    public $css = [
        'cropper.css'
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'cropper.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        JqueryAsset::class,
    ];
}