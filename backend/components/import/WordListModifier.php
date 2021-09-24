<?php

namespace backend\components\import;

use DomainException;

class WordListModifier
{

    protected const MAX_ANSWER_NUMBER = 5;
    protected $words;

    public function __construct(array $words)
    {
        $this->words = $words;
        if (count($this->words) === 0) {
            throw new DomainException('Список слов пуст');
        }
    }

    protected function createQuestion(string $name): QuestionDto
    {
        return new QuestionDto($name);
    }
}