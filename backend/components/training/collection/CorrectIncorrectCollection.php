<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class CorrectIncorrectCollection extends BaseCollection
{

    public const CORRECT = 1;
    public const INCORRECT = 2;

    public function createQuestion(int $testID, $questionData, $stars)
    {
        $question = new WordQuestion($testID, $questionData->id, $questionData->name, $stars);
        $question->addAnswer(new Answer(self::CORRECT, 'Правильно', true));
        $question->addAnswer(new Answer(self::INCORRECT, 'Неправильно', false));
        return $question;
    }

}
