<?php


namespace backend\components\question;


class QuestionCollection
{

    private $questions = [];

    public function add($question)
    {
        $this->questions[] = $question;
    }

}