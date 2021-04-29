<?php

namespace backend\components\training\base;

class QuestionCollection
{

    /** @var int */
    private $total;

    /** @var BaseQuestion[] */
    private $questions = [];

    /** @var bool */
    private $fastMode;

    public function __construct(int $total, bool $fastMode = false)
    {
        $this->total = $total;
        $this->fastMode = $fastMode;
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

    public function serialize($shuffle = false)
    {
        if ($shuffle) {
            shuffle($this->questions);
        }
        $total = $this->fastMode ? 1 : 5;
        return array_map(static function($item) use ($total) {
            $value = $item->serialize();
            $value['stars']['total'] = $total;
            return $value;
        }, $this->questions);
    }

}