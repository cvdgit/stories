<?php

namespace backend\components\training\base;

class QuestionCollection
{

    /** @var int */
    private $total;

    /** @var BaseQuestion[] */
    private $questions = [];

    public function __construct(int $total)
    {
        $this->total = $total;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function addQuestion(BaseQuestion $question)
    {
        $this->questions[] = $question;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function serialize()
    {
        return array_map(static function($item) {
            return $item->serialize();
        }, $this->questions);
    }

}