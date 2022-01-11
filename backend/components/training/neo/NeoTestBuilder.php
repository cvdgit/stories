<?php

namespace backend\components\training\neo;

use backend\components\training\base\TestParams;
use backend\components\training\base\UserProgress;

class NeoTestBuilder
{

    private $data;
    private $userProgress;
    private $repeat;

    private $questionBuilder;

    public function __construct(array $data, UserProgress $userProgress, int $repeat)
    {
        $this->data = $data;
        $this->userProgress = $userProgress;
        $this->repeat = $repeat;

        $this->questionBuilder = new NeoQuestionsBuilder($data['questions'], $userProgress, $repeat);
    }

    public function build(array $students, TestParams $testParams): array
    {
        $questions = $this->questionBuilder->build();
        return [0 => [
            'storyTestQuestions' => $questions,
            'test' => [
                'id' => $testParams->getId(),
                'progress' => [
                    'total' => count($questions) * $this->repeat,
                    'current' => $this->userProgress->getStarsCount(),
                ],
                'incorrectAnswerText' => $testParams->getIncorrectAnswerText(),
                'showAnswerImage' => filter_var($this->data['showAnswerImage'], FILTER_VALIDATE_BOOLEAN),
                'showAnswerText' => filter_var($this->data['showAnswerText'], FILTER_VALIDATE_BOOLEAN),
                'showQuestionImage' => filter_var($this->data['showQuestionImage'], FILTER_VALIDATE_BOOLEAN),
                'answerType' => 0,
                'source' => $testParams->getSource(),
                'repeatQuestions' => $this->repeat,
            ],
            'students' => $students,
            'incorrectAnswerAction' => $this->data['incorrectAnswerAction'],
            'params' => $this->data['params'],
            'code' => $this->data['code'],
        ]];
    }
}
