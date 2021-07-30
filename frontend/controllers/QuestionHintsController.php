<?php

namespace frontend\controllers;

use common\models\StoryTestQuestion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class QuestionHintsController extends Controller
{

    private function findQuestionModel(int $id)
    {
        if (($model = StoryTestQuestion::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionView(int $question_id)
    {
        $questionModel = $this->findQuestionModel($question_id);
        $slides = [];
        foreach ($questionModel->storySlides as $slideModel) {
            $search = [
                'data-id=""',
                'data-background-color="#000000"',
            ];
            $replace = [
                'data-id="' . $slideModel->id . '"',
                'data-background-color="#fff"',
            ];
            $slides[$slideModel->id] = str_replace($search, $replace, $slideModel->data);
        }

        $data = '<div class="slides">' . implode('', $slides) . '</div>';

        if (class_exists('yii\debug\Module')) {
            $this->view->off(\yii\web\View::EVENT_END_BODY, [\yii\debug\Module::getInstance(), 'renderToolbar']);
        }

        return $this->renderAjax('player', [
            'model' => $slideModel->story,
            'data' => $data,
        ]);
    }
}
