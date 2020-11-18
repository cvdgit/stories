<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class InputCollection extends BaseCollection
{

    public function createQuestion(int $testID, $questionData, $stars)
    {
        $question = new WordQuestion($testID, $questionData->id, 'Введите текст', $stars);
        $correctAnswer = empty($questionData->correct_answer) ? $questionData->name : $questionData->correct_answer;
        $question->addAnswer(new Answer(1, $correctAnswer, true));
        return $question;
    }

}
