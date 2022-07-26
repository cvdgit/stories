<?php

namespace backend\components\import;

use common\models\TestWord;
use yii\helpers\ArrayHelper;

class DefaultWordProcessor implements WordProcessor
{

    private $words;
    private $numberAnswers;

    /**
     * @param array $words
     * @param int $numberAnswers
     */
    public function __construct(array $words, int $numberAnswers)
    {
        $this->words = $words;
        $this->numberAnswers = $numberAnswers;
    }

    public function createIncorrectAnswers(TestWord $current, int $max): array
    {
        $incorrect = array_filter($this->words, static function(TestWord $item) use ($current) {
            return $item->id !== $current->id && $item->correct_answer !== $current->correct_answer;
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
            return new AnswerDto($item->correct_answer, false);
        }, $incorrect);
    }

    public function process(TestWord $word): QuestionDto
    {
        $question = new QuestionDto($word->name);

        $answers = [new AnswerDto($word->correct_answer, true)];
        if ($this->numberAnswers > 1) {
            $max = $this->numberAnswers - count($answers);
            $answers = array_merge($answers, $this->createIncorrectAnswers($word, $max));
            shuffle($answers);
        }

        $question->setAnswers($answers);

        return $question;
    }
}
