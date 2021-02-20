<?php

namespace backend\components\training\local;

use backend\components\training\base\BaseQuestion;

class Question extends BaseQuestion
{

    /** @var int */
    private $mixAnswers;

    /** @var int */
    private $type;

    public function __construct(int $id, string $name, bool $lastAnswerIsCorrect, int $mixAnswers, int $type, $image = null)
    {
        parent::__construct($id, $name, $lastAnswerIsCorrect, $image);
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

    protected function makeStars(array $starsData, BaseQuestion $question)
    {
        $stars = 0;
        $questionID = $question->getId();
        foreach ($starsData as $star) {
            if ((int)$star['entity_id'] === $questionID) {
                $stars = $star['stars'];
                break;
            }
        }
        return $stars;
    }

}