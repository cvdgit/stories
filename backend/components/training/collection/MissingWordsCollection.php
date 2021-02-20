<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\MissingWordsQuestion;
use common\models\TestWord;

class MissingWordsCollection extends BaseCollection
{

    public function createQuestion($questionData, $stars)
    {
        /** @var $questionData TestWord */
        $question = new MissingWordsQuestion($questionData->id, $questionData->name, $stars);
        $question->addAnswer(new Answer(1, strtr($questionData->name, ['{' => '', '}' => '']), true));
        return $question;
    }
}
