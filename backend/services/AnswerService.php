<?php

namespace backend\services;

use backend\models\answer\DefaultAnswerModel;
use backend\models\answer\SequenceAnswerModel;
use common\models\StoryTestAnswer;
use DomainException;

class AnswerService
{

    public function createAnswer(DefaultAnswerModel $form): StoryTestAnswer
    {
        if (!$form->validate()) {
            throw new DomainException('DefaultAnswerModel not valid');
        }
        return StoryTestAnswer::createFromRelation(
            $form->name,
            $form->correct,
            $form->description
        );
    }

    public function createSequenceAnswer(SequenceAnswerModel $form): StoryTestAnswer
    {
        if (!$form->validate()) {
            throw new DomainException('DefaultAnswerModel not valid');
        }
        return StoryTestAnswer::createSequenceFromRelation(
            $form->name,
            $form->order
        );
    }
}
