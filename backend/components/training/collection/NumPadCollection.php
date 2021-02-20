<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\WordQuestion;

class NumPadCollection extends BaseCollection
{

    public function createQuestion($questionData, $stars)
    {
        $question = new WordQuestion($questionData->id, $questionData->name, $stars);
        $question->addAnswer(new Answer(1, $questionData->correct_answer, true));
        return $question;
    }

}
