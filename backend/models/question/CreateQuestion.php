<?php

namespace backend\models\question;

use common\models\StoryTestQuestion;
use DomainException;

class CreateQuestion extends QuestionModel
{

    public function __construct(int $quizId, $config = [])
    {
        $this->story_test_id = $quizId;
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        $this->type = QuestionType::ONE;
        $this->order = null;
        $this->mix_answers = true;
    }

    public function create()
    {
        if (!$this->validate()) {
            throw new DomainException('Model is not valid');
        }
        $model = StoryTestQuestion::create($this->story_test_id, $this->name, $this->type, $this->order, $this->mix_answers);
        $model->hint = $this->hint;
        $this->uploadImage($model);
        $model->save();
        return $model->id;
    }

    public function getStorySlides()
    {
        return [];
    }
}
