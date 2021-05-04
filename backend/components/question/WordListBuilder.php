<?php

namespace backend\components\question;

use backend\components\question\base\DefaultQuestion;

class WordListBuilder extends QuestionCollectionBuilder
{

    public function __construct()
    {
    }

    public function createQuestion()
    {
        return new DefaultQuestion();
    }
}