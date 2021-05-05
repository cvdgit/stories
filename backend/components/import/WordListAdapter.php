<?php

namespace backend\components\import;

use backend\models\question\QuestionType;
use common\models\TestWord;
use common\models\TestWordList;
use DomainException;

class WordListAdapter
{

    private $wordList;
    private $words;

    public function __construct(TestWordList $wordList)
    {
        $this->wordList = $wordList;
        $this->words = $wordList->testWords;
        if ($this->words === null || count($this->words) === 0) {
            throw new DomainException('Список слов пуст');
        }
    }

    private function createAnswer(string $name, bool $correct): array
    {
        return [
            'name' => $name,
            'correct' => $correct,
        ];
    }

    private function createIncorrectAnswers(TestWord $current, int $max): array
    {
        $incorrect = array_filter($this->words, static function(TestWord $item) use ($current) {
            return $item->id !== $current->id && !empty($item->correct_answer);
        });
        if (count($incorrect) === 0) {
            return [];
        }

        if (count($incorrect) < $max) {
            $max = count($incorrect);
        }

        $keys = array_rand($incorrect, $max);
        $incorrect = array_map(static function($key) use ($incorrect) {
            return $incorrect[$key];
        }, $keys);

        return array_map(function(TestWord $item) {
            return $this->createAnswer($item->correct_answer, false);
        }, $incorrect);
    }

    public function create(int $numberAnswers): array
    {
        $questions = [];
        foreach ($this->words as $word) {
            if (empty($word->correct_answer)) {
                continue;
            }
            $question = [
                'name' => $word->name,
                'type' => QuestionType::ONE,
            ];
            $answers = [$this->createAnswer($word->correct_answer, true)];
            $max = $numberAnswers - count($answers);
            $answers = array_merge($answers, $this->createIncorrectAnswers($word, $max));
            shuffle($answers);
            $question['answers'] = $answers;
            $questions[] = $question;
        }
        return $questions;
    }

}