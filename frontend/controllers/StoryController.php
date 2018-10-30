<?php

namespace frontend\controllers;

use Yii;
use common\models\Story;
use common\models\StorySearch;
use yii\web\NotFoundHttpException;

class StoryController extends \yii\web\Controller
{

    public function actionIndex()
    {
    	$searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Story model.
     * @param string $alias
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($alias)
    {
        return $this->render('view', [
            'model' => $this->findModelByAlias($alias),
        ]);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    protected function findModelByAlias($alias)
    {
        if (($model = Story::findOne(['alias' => $alias])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    public function actionViewByFrame($id)
    {
        return $this->renderPartial('frame', ['model' => $this->findModel($id)]);
    }

}
