<?php

namespace frontend\controllers;

use Yii;
use common\models\Story;
use common\models\StoryFeedback;

class FeedbackController extends \yii\web\Controller
{

	public function actionCreate($id)
	{
		
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$response = ['success' => false];

		if (Yii::$app->request->isAjax) {
		
			$story = Story::findOne($id);
			if ($story !== null) {
				$post = Yii::$app->request->post();
				$response['success'] = StoryFeedback::createFeedback($story->id, $post['slide_number']);
			}
		}
		
		return $response;
	}

}
