<?php

namespace backend\models\question;

use common\models\StoryTestQuestion;

class CreateQuestion extends QuestionModel
{

    public function init()
    {
        parent::init();
        $this->type = QuestionType::ONE;
        $this->order = 1;
        $this->mix_answers = true;
    }

    public function create()
    {
        if (!$this->validate()) {
            throw new DomainException('Model is not valid');
        }
        $model = StoryTestQuestion::create($this->story_test_id, $this->name, $this->type, $this->order, $this->mix_answers);
        $this->uploadImage($model);
        $model->save();
        return $model->id;
    }

}