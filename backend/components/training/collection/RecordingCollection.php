<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\RememberQuestion;

class RecordingCollection extends BaseCollection
{

    /** @var bool */
    private $rememberAnswers;

    public function createQuestion(int $testID, $questionData, $stars)
    {
        $remember = $this->rememberAnswers;
        if ($remember) {
            $remember = empty($questionData->correct_answer);
        }
        $question = new RememberQuestion($testID, $questionData->id, $questionData->name, $stars, $remember);
        $question->addAnswer(new Answer(1, $this->createCorrectAnswer($questionData), true));
        return $question;
    }

    private function createCorrectAnswer($data): string
    {
        if ($this->rememberAnswers) {
            $correctAnswer = empty($data->correct_answer) ? '' : $data->correct_answer;
        }
        else {
            $correctAnswer = empty($data->correct_answer) ? $data->name : $data->correct_answer;
        }
        $correctAnswer = trim(preg_replace('/[^A-ZА-Я0-9\-\sё]/ui', '', $correctAnswer));
        return $correctAnswer;
    }

    public function setRememberAnswers(bool $value): void
    {
        $this->rememberAnswers = $value;
    }

}
