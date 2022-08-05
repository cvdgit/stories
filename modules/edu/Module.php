<?php

namespace modules\edu;

use modules\edu\assets\AppAsset;
use Yii;

/**
 * edu module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\edu\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        AppAsset::register(Yii::$app->view);
        parent::init();
    }
/*
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }*/
}
