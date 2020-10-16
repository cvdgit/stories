<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class RecordingCollection extends BaseCollection
{

    public function createQuestion(int $testID, array $word, int $stars)
    {
        $question = new WordQuestion($testID, $word['id'], $word['name'], true,0, 0, $stars);
        $correctAnswer = empty($word['correct_answer']) ? $word['name'] : $word['correct_answer'];
        $correctAnswer = trim(preg_replace('/[^A-ZА-Я0-9\-\sё]/ui', '', $correctAnswer));
        $question->addAnswer(new Answer(1, $correctAnswer, true));
        return $question;
    }

    public function getCorrectValue(array $word): int
    {
        return $word['id'];
    }

}
