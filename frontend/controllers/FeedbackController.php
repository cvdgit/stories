<?php

namespace frontend\controllers;

use common\models\StorySlide;
use Yii;
use common\models\Story;
use common\models\StoryFeedback;
use yii\web\Controller;
use yii\web\Response;

class FeedbackController extends Controller
{

	public function actionCreate($id)
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$response = ['success' => false];
		if (Yii::$app->request->isAjax) {
			$story = Story::findOne($id);
			$post = Yii::$app->request->post();
			$slideID = (int)$post['slide_number'];
			$slide = StorySlide::findSlide($slideID);
			$response['success'] = StoryFeedback::createFeedback($slide);
		}
		return $response;
	}

}
