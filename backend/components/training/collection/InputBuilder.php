<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class InputBuilder extends BaseCollection
{

    public function createQuestion(int $testID, array $word, int $stars)
    {
        $question = new WordQuestion($testID, $word['id'], 'Введите слово', true,0, 0, $stars);
        $correctAnswer = empty($word['correct_answer']) ? $word['name'] : $word['correct_answer'];
        $question->addAnswer(new Answer(1, $correctAnswer, true));
        return $question;
    }

    public function getCorrectValue(array $word): int
    {
        return $word['id'];
    }

}
