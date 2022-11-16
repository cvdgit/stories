<?php

declare(strict_types=1);

namespace backend\components\import;

use common\models\TestWord;
use DomainException;

class WordListAdapter
{
    private $words;
    private $processor;

    /**
     * @param TestWord[] $words
     * @param WordProcessor $processor
     */
    public function __construct(array $words, WordProcessor $processor)
    {
        if (count($words) === 0) {
            throw new DomainException('Список слов пуст');
        }
        $this->words = $words;
        $this->processor = $processor;
    }

    public function process(): array
    {
        $questions = [];
        foreach ($this->words as $word) {

            $str = $word->name;
            if (empty($str)) {
                continue;
            }

            $questions[] = $this->processor->process($word);
        }
        return $questions;
    }
}
