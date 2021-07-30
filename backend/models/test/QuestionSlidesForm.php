<?php

namespace backend\models\test;

use common\models\StoryTestQuestion;
use common\models\StoryTestQuestionStorySlide;
use yii\base\Model;

class QuestionSlidesForm extends Model
{

    public $question_id;
    public $slide_ids = [];

    public function rules()
    {
        return [
            ['question_id', 'integer'],
            ['slide_ids', 'each', 'rule' => ['integer']],
        ];
    }

    public function create(StoryTestQuestion $questionModel): void
    {
        if (!$this->validate()) {
            throw new \DomainException('QuestionSlidesForm is not valid');
        }
        StoryTestQuestionStorySlide::deleteByQuestionID($questionModel->id);
        $order = 1;
        foreach ($this->slide_ids as $slideID) {
            $model = StoryTestQuestionStorySlide::create($questionModel->id, $slideID, $order);
            $model->save();
            $order++;
        }
        $questionModel->refresh();
    }
}
