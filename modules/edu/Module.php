<?php

namespace modules\edu;

use common\rbac\UserRoles;
use modules\edu\assets\AppAsset;
use modules\edu\components\EduAccessChecker;
use Yii;
use yii\web\Application as WebApp;
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

    private $accessChecker;

    public function __construct($id, $parent = null,EduAccessChecker $accessChecker = null, $config = [])
    {
        parent::__construct($id, $parent, $config);
        $this->accessChecker = $accessChecker;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function($rule, $action) {
                            return $this->accessChecker->canUserAccess(Yii::$app->user->getId());
                        }
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
        parent::init();

        if (Yii::$app instanceof WebApp) {
            AppAsset::register(Yii::$app->view);
        }
    }

/*
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }*/
}
