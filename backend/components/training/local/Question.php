<?php

namespace backend\components\training\local;

use backend\components\training\base\BaseQuestion;

class Question extends BaseQuestion
{

    /** @var int */
    private $mixAnswers;

    /** @var int */
    private $type;

    /**
     * @param int $id
     * @param string $name
     * @param bool $lastAnswerIsCorrect
     * @param int $mixAnswers
     * @param int $type
     * @param $image
     * @param $origImage
     * @param $hint
     * @param $audioFile
     */
    public function __construct(int $id, string $name, bool $lastAnswerIsCorrect, int $mixAnswers, int $type, $image = null, $origImage = null, $hint = null, $audioFile = null)
    {
        parent::__construct($id, $name, $lastAnswerIsCorrect, $image, $origImage, $hint, $audioFile);
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
