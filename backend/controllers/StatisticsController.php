<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\Story;
use common\models\StoryStatisticsSearch;

class StatisticsController extends \backend\components\AdminController
{

	public function actionList($id)
	{
		$model = Story::findOne($id);
		$searchModel = new StoryStatisticsSearch();
		//$dataProvider = $searchModel->search($id, Yii::$app->request->queryParams);
        $chartData = $searchModel->getChartData($id);
        $chartData2 = $searchModel->getChartData2($id);
        $chartData3 = $searchModel->getChartData3($id);
		return $this->render('list', [
			'model' => $model,
            'chartData' => $chartData,
            'chartData2' => $chartData2,
            'chartData3' => $chartData3,
			//'searchModel' => $searchModel,
			//'dataProvider' => $dataProvider,
		]);
	}

    public function actionView($id)
    {

        return $this->render('view', []);
    }

}
