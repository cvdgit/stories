<?php

namespace modules\edu;

use common\rbac\UserRoles;
use modules\edu\assets\AppAsset;
use Yii;
use yii\filters\AccessControl;

/**
 * edu module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\edu\controllers';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_USER],
                    ]
                ]
            ]
        ];
    }

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
