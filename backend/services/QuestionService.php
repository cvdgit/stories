<?php

namespace backend\services;

use backend\models\question\CreateQuestion;
use backend\models\question\sequence\CreateSequenceQuestion;
use common\models\StoryTestQuestion;
use DomainException;

class QuestionService
{

    public function createQuestion(CreateQuestion $form): StoryTestQuestion
    {
        if (!$form->validate()) {
            throw new DomainException(implode(PHP_EOL, $form->getErrorSummary(true)));
        }
        return StoryTestQuestion::create(
            $form->story_test_id,
            $form->name,
            $form->type,
            $form->order
        );
    }

    public function createSequenceQuestion(CreateSequenceQuestion $form): StoryTestQuestion
    {
        if (!$form->validate()) {
            throw new DomainException('CreateSequenceQuestion not valid');
        }
        return StoryTestQuestion::createSequence(
            $form->story_test_id,
            $form->name,
            $form->order,
            $form->sort_view
        );
    }
}
