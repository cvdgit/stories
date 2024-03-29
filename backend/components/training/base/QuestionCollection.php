<?php

declare(strict_types=1);

namespace backend\components\training\base;

class QuestionCollection
{
    /** @var int */
    private $total;

    /** @var BaseQuestion[] */
    private $questions = [];

    /** @var int */
    private $starsTotal;

    public function __construct(int $total, int $starsTotal)
    {
        $this->total = $total;
        $this->starsTotal = $starsTotal;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function addQuestion(BaseQuestion $question): void
    {
        $this->questions[] = $question;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function serialize($shuffle = false): array
    {
        if ($shuffle) {
            shuffle($this->questions);
        }
        return array_map(function($item) {
            $value = $item->serialize();
            $value['stars']['total'] = $this->starsTotal;
            return $value;
        }, $this->questions);
    }

    public function count(): int
    {
        return count($this->questions);
    }
}
