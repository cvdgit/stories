<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class NumPadCollection extends BaseCollection
{

    public function createQuestion(int $testID, $questionData, $stars)
    {
        $question = new WordQuestion($testID, $questionData->id, $questionData->name, $stars);
        $question->addAnswer(new Answer(1, $questionData->correct_answer, true));
        return $question;
    }

}
