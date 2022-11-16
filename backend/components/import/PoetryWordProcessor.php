<?php

declare(strict_types=1);

namespace backend\components\import;

use ArrayObject;
use common\models\TestWord;

class PoetryWordProcessor implements WordProcessor
{
    /** @var TestWord[] */
    private $words;

    public function __construct(array $words)
    {
        $this->words = $words;
    }

    private function createIncorrectAnswers(TestWord $current, TestWord $next, int $max): array
    {
        $incorrect = array_filter($this->words, static function(TestWord $item) use ($current, $next) {
            return $item->id !== $current->id
                && $item->name !== $current->name
                && $item->id !== $next->id
                && $item->name !== $next->name;
        });

        if (count($incorrect) === 0) {
            return [];
        }

        if (count($incorrect) < $max) {
            $max = count($incorrect);
        }

        $keys = array_rand($incorrect, $max);
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $incorrect = array_map(static function($key) use ($incorrect) {
            return $incorrect[$key];
        }, $keys);

        return array_map(static function(TestWord $item) {
            return new AnswerDto($item->name, false);
        }, $incorrect);
    }

    public function process(TestWord $word): QuestionDto
    {
        $question = new QuestionDto($word->name);

        $values = array_filter($this->words, static function(TestWord $item) use ($word) {
            return $item->order > $word->order;
        });
        $nextWord = current($values);

        if (!$nextWord) {
            return $question;
        }

        $answers = [new AnswerDto($nextWord->name, true)];

        $max = 5 - count($answers);
        $answers = array_merge($answers, $this->createIncorrectAnswers($word, $nextWord, $max));
        shuffle($answers);

        $question->setAnswers($answers);

        return $question;
    }
}
