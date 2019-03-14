<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\StoryFeedback;
use common\models\StoryFeedbackSearch;

class FeedbackController extends \backend\components\AdminController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['moderator'],
                    ],
                ],
            ],
        ];
    }

	public function actionIndex()
	{
        $searchModel = new StoryFeedbackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
	}

    public function actionBatchupdate()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = ['success' => false];
        $post = Yii::$app->request->post();
        $response['success'] = StoryFeedback::updateStatus($post['data']);
        return $response;
    }

}
