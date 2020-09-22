<?php

namespace backend\components\training\base;

class AnswerSerializer
{

    private $answer;

    public function __construct(Answer $answer)
    {
        $this->answer = $answer;
    }

    public function serialize(): array
    {
        return [
            'id' => $this->answer->getId(),
            'name' => $this->answer->getName(),
            'is_correct' => (int) $this->answer->isCorrect(),
            'description' => '',
        ];
    }

}