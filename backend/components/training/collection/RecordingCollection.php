<?php

namespace backend\components\training\collection;

use backend\components\training\base\Answer;
use backend\components\training\local\RememberQuestion;
use backend\components\WordListFormatter;

class RecordingCollection extends BaseCollection
{

    /** @var bool */
    private $rememberAnswers;

    private $wordListFormatter;

    public function __construct($data, $stars, WordListFormatter $wordListFormatter)
    {
        parent::__construct($data, $stars);
        $this->wordListFormatter = $wordListFormatter;
    }

    public function createQuestion($questionData, $stars)
    {
        $remember = $this->rememberAnswers;
        if ($remember) {
            $remember = empty($questionData->correct_answer);
        }
        $question = new RememberQuestion($questionData->id, $questionData->name, $stars, $remember);
        $question->addAnswer(new Answer(1, $this->createCorrectAnswer($questionData), true));
        return $question;
    }

    private function createCorrectAnswer($data): string
    {
        $rememberAnswersCondition = $this->rememberAnswers ? '' : $data->name;
        $correctAnswer = empty($data->correct_answer) ? $rememberAnswersCondition : $data->correct_answer;

        $correctAnswer = trim($correctAnswer);
        $matches = [];
        if ($this->wordListFormatter->haveMatches($correctAnswer, $matches)) {
            //$correctAnswer = str_replace($matches[0], $matches[2], $correctAnswer);
        }
        else {
            $correctAnswer = preg_replace('/[^A-ZА-Я0-9\-\sё]/ui', '', $correctAnswer);
        }
        return $correctAnswer;
    }

    public function setRememberAnswers(bool $value): void
    {
        $this->rememberAnswers = $value;
    }

}
