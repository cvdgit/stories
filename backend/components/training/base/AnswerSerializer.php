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
            'orig_image' => $this->answer->getOrigImage(),
            'original_image' => $this->answer->getOrigImage() !== '',
            'order' => (int) $this->answer->getOrder(),
        ];
    }

}