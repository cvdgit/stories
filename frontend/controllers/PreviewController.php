<?php

namespace frontend\controllers;

use common\models\Story;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PreviewController extends Controller
{

    public $defaultAction = 'view';

    public function actionView(string $alias)
    {
        $model = $this->findStoryModelByAlias($alias);
        if (!Yii::$app->user->isGuest) {
            return $this->redirect($model->getStoryUrl());
        }
        if (!$model->linkAccessAllowed()) {
            return $this->goHome();
        }
        return $this->render('view', ['model' => $model]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findStoryModelByAlias(string $alias): ?Story
    {
        if (($model = Story::findOne(['alias' => $alias])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}