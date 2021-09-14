<?php

namespace backend\widgets;

use common\models\StoryTestQuestion;
use yii\base\Widget;

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
            'slides' => $slides,
            'questionID' => $this->model->id,
        ]);
    }
}
