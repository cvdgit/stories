<?php

namespace backend\components\import;

use backend\components\WordListFormatter;
use common\models\TestWord;

class SequenceWordProcessor implements WordProcessor
{

    /**
     * @inheritDoc
     */
    public function process(TestWord $word): QuestionDto
    {
        $str = $word->name;
        $question = new QuestionDto($str);

        $stringWords = WordListFormatter::stringAsWords($str);
        foreach ($stringWords as $stringWord) {
            $question->addAnswer(new AnswerDto($stringWord, true));
        }

        return $question;
    }
}
