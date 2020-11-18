<?php

namespace backend\components\training\local;

use backend\components\training\base\BaseQuestion;

class Question extends BaseQuestion
{

    /** @var int */
    private $mixAnswers;

    /** @var int */
    private $type;

    public function __construct(int $testID, int $id, string $name, bool $lastAnswerIsCorrect, int $mixAnswers, int $type, $image = null)
    {
        parent::__construct($testID, $id, $name, $lastAnswerIsCorrect, $image);
        $this->mixAnswers = $mixAnswers;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getMixAnswers(): int
    {
        return $this->mixAnswers;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    public function serialize()
    {
        return array_merge([
            'type' => $this->getType(),
            'mix_answers' => $this->getMixAnswers(),
        ], parent::serialize());
    }

}