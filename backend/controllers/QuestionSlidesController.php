<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\models\test\QuestionSlidesForm;
use common\models\Story;
use common\models\StoryTestQuestion;
use Yii;
use yii\web\Response;

class QuestionSlidesController extends BaseController
{

    public function actionManage(int $question_id)
    {
        $questionModel = $this->findModel(StoryTestQuestion::class, $question_id);
        return $this->renderAjax('manage', [
            'questionModel' => $questionModel,
        ]);
    }

    public function actionSlides(int $story_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var Story $model */
        $model = $this->findModel(Story::class, $story_id);
        return $model->getSlidesForQuestion();
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new QuestionSlidesForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** @var StoryTestQuestion $questionModel */
            $questionModel = $this->findModel(StoryTestQuestion::class, $model->question_id);
            $model->create($questionModel);
            return ['success' => true, 'slides' => $questionModel->getStorySlidesForList()];
        }
        return ['success' => false];
    }
}
