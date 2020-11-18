<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class RecordingCollection extends BaseCollection
{

    public function createQuestion(int $testID, $questionData, $stars)
    {
        $question = new WordQuestion($testID, $questionData->id, $questionData->name, $stars);
        $correctAnswer = empty($questionData->correct_answer) ? $questionData->name : $questionData->correct_answer;
        $correctAnswer = trim(preg_replace('/[^A-ZА-Я0-9\-\sё]/ui', '', $correctAnswer));
        $question->addAnswer(new Answer(1, $correctAnswer, true));
        return $question;
    }

}
