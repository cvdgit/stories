<?php

declare(strict_types=1);

namespace backend\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BaseController extends Controller
{
    /**
     * @template T
     * @param class-string<T> $modelClassName
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @return T
     */
    public function findModel(string $modelClassName, int $id): object
    {
        $modelObject = Yii::createObject($modelClassName);
        if (($model = $modelObject::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
