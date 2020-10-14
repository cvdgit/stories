<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class CorrectIncorrectBuilder extends BaseCollection
{

    public const CORRECT = 1;
    public const INCORRECT = 2;

    public function createQuestion(int $testID, array $word, int $stars)
    {
        $question = new WordQuestion($testID, $word['id'], $word['name'], true,0, 0, $stars);
        $question->addAnswer(new Answer(self::CORRECT, 'Правильно', true));
        $question->addAnswer(new Answer(self::INCORRECT, 'Неправильно', false));
        return $question;
    }

    public function getCorrectValue(array $word): int
    {
        return self::CORRECT;
    }

}
