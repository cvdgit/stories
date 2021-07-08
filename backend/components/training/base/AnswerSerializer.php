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
            'name' => trim($this->answer->getName()),
            'is_correct' => (int) $this->answer->isCorrect(),
            'description' => '',
            'region_id' => $this->answer->getRegionID(),
            'image' => $this->answer->getImage(),
            'orig_image' => $this->answer->getImage(),
            'original_image' => true,
            'order' => (int) $this->answer->getOrder(),
        ];
    }

}