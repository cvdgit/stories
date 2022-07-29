<?php

namespace modules\edu;

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
        parent::init();
    }

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }
}
