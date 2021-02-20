<?php

namespace backend\components\training\local;

class WordQuestion extends Question
{

    private $starsTotal = 5;
    private $stars;
    private $questionID;

    public function __construct(int $questionID, string $name, array $stars)
    {
        parent::__construct($questionID, $name, true, 0, 0);
        $this->stars = $stars;
        $this->questionID = $questionID;
    }

    public function serialize()
    {
        return array_merge([
            'stars' => [
                'total' => $this->starsTotal,
                'current' => $this->makeStars($this->stars, $this),
            ],
            'view' => 'word',
        ], parent::serialize());
    }

    public function getCorrectAnswerIDs()
    {
        return [$this->questionID];
    }

}