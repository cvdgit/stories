<?php

namespace backend\widgets;

use common\models\StoryTestQuestion;
use yii\base\Widget;
use yii\helpers\Json;
use yii\helpers\Url;

class QuestionSlidesWidget extends Widget
{

    /** @var StoryTestQuestion */
    public $model;

    public function run()
    {
        if ($this->model === null) {
            return '';
        }
        $slides = $this->model->getStorySlidesForList();
        return $this->render('question-slides', [
            'slides' => Json::encode($slides),
            'remote' => Url::to(['question-slides/manage', 'question_id' => $this->model->id]),
        ]);
    }
}
