<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class InputBuilder extends BaseCollection
{

    public function createQuestion(int $testID, array $word, int $stars)
    {
        $question = new WordQuestion($testID, $word['id'], 'Введите слово', true,0, 0, $stars);
        $question->addAnswer(new Answer(1, $word['name'], true));
        return $question;
    }

    public function getCorrectValue(array $word): int
    {
        return $word['id'];
    }

}
