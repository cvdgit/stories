<?php

namespace frontend\controllers;

use Yii;
use common\models\Story;
use common\models\StoryStatistics;
use yii\web\Controller;

class StatisticsController extends Controller
{

	public function actionWrite($id)
	{
		$story = Story::findOne($id);
		if ($story !== null) {
			
			$post = Yii::$app->request->post();

			$model = new StoryStatistics();
			$model->story_id = $story->id;
			$model->slide_number = $post['slide_number'];
			$model->begin_time = $post['begin_time'];
			$model->end_time = $post['end_time'];
			$model->chars = $post['chars'];
			$model->session = $post['session'];
			$model->save();
			print_r($model->getErrors());
		}
	}

}
