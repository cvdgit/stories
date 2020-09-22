<?php

namespace backend\components\training\local;

class WordQuestion extends Question
{

    private $starsTotal = 5;
    private $starsCurrent;

    public function __construct(int $testID, int $id, string $name, bool $lastAnswerIsCorrect, int $mixAnswers, int $type, int $starsCurrent, $image = null)
    {
        parent::__construct($testID, $id, $name, $lastAnswerIsCorrect, $mixAnswers, $type, $image);
        $this->starsCurrent = $starsCurrent;
    }

    public function serialize()
    {
        return array_merge([
            'stars' => [
                'total' => $this->starsTotal,
                'current' => $this->starsCurrent,
            ]
        ], parent::serialize());
    }

}