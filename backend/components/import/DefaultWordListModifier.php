<?php

namespace backend\components\import;

use common\models\TestWord;

class DefaultWordListModifier extends WordListModifier implements WordListModifierInterface
{

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
        return array_map(static function($key) use ($incorrect) {
            return $incorrect[$key];
        }, $keys);
    }

    public function modify(): array
    {
        $questions = [];
        foreach ($this->words as $word) {

            if (empty($word->correct_answer)) {
                continue;
            }

            $question = $this->createQuestion($word->name);
            $question->createAnswer($word->correct_answer, true, $word->name);

            $max = self::MAX_ANSWER_NUMBER - $question->getAnswersCount();
            foreach ($this->createIncorrectAnswers($word, $max) as $incorrectWord) {
                $question->createAnswer($incorrectWord->correct_answer, false, $incorrectWord->name);
            }

            $questions[] = $question->shuffleAnswers();
        }
        return $questions;
    }
}
