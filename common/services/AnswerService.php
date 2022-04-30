<?php

namespace common\services;

use backend\components\training\base\Answer;
use backend\components\training\base\AnswerSerializer;
use common\models\StoryTestAnswer;
use frontend\models\AnswerCreateHiddenForm;

class AnswerService
{

    public function createHidden(AnswerCreateHiddenForm $form): StoryTestAnswer
    {
        if (!$form->validate()) {
            throw new \DomainException('AnswerCreateHiddenForm not valid');
        }
        $answerModel = StoryTestAnswer::create($form->question_id, $form->answer, StoryTestAnswer::CORRECT_ANSWER);
        $answerModel->answerSetHidden();
        if (!$answerModel->save()) {
            throw new \DomainException('StoryTestAnswer save exception');
        }
        return $answerModel;
    }

    public function serializeModel(StoryTestAnswer $answerModel): array
    {
        $answer = new Answer($answerModel->id, $answerModel->name, $answerModel->answerIsCorrect());
        $answer->setHidden($answerModel->hidden);
        return (new AnswerSerializer($answer))->serialize();
    }
}
