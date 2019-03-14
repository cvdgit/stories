<?php
namespace backend\controllers;

use Yii;
use common\models\Tag;

/**
 * Site controller
 */
class TagController extends \backend\components\AdminController
{

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
