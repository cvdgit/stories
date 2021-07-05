<?php

namespace backend\components;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BaseController extends Controller
{

    /**
     * @throws yii\base\InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function findModel(string $modelClassName, int $id)
    {
        $modelObject = Yii::createObject($modelClassName);
        if (($model = $modelObject::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}