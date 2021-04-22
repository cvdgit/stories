<?php

namespace backend\models\question\sequence;

use backend\models\question\QuestionType;
use common\models\StoryTestQuestion;
use DomainException;

class CreateSequenceQuestion extends SequenceQuestion
{

    public function __construct(int $testID, $config = [])
    {
        parent::__construct($config);
        $this->story_test_id = $testID;
        $this->order = 1;
    }

    public function init()
    {
        parent::init();
        $this->type = QuestionType::SEQUENCE;
        $this->name = 'Восстановите последовательность';
    }

    public function createQuestion(): int
    {
        if (!$this->validate()) {
            throw new DomainException('Model is not valid');
        }
        $model = StoryTestQuestion::createSequence($this->story_test_id, $this->name, $this->order);
        $model->save();
        return $model->id;
    }
}