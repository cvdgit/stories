<?php

namespace backend\components\import;

use backend\components\WordListFormatter;
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

    private function createQuestion(string $name, int $type): array
    {
        return [
            'name' => $name,
            'type' => $type,
            'answers' => [],
        ];
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

    public function createSequence(int $type): array
    {
        $questions = [];
        foreach ($this->words as $word) {

            $str = $word->name;
            if (empty($str)) {
                continue;
            }

            $stringWords = WordListFormatter::stringAsWords($str);
            if (count($stringWords) === 0) {
                continue;
            }

            $question = $this->createQuestion($word->name, $type);
            foreach ($stringWords as $stringWord) {
                $question['answers'][] = $this->createAnswer($stringWord, true);
            }
            $questions[] = $question;
        }
        return $questions;
    }
}
