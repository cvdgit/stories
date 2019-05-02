<?php
namespace backend\controllers;

use common\rbac\UserRoles;
use Yii;
use common\models\Tag;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class TagController extends \backend\components\AdminController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_TAGS_ACCESS],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionList($query)
    {
        $models = Tag::findAllByName($query);
        $items = [];
        foreach ($models as $model) {
            $items[] = ['name' => $model->name];
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $items;
    }
}
