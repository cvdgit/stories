<?php

namespace backend\components\training\collection;

use backend\components\training\base\QuestionCollection;

abstract class BaseCollection
{

    private $testID;
    private $data;
    private $stars;

    public function __construct(int $testID, $data, $stars)
    {
        $this->testID = $testID;
        $this->data = $data;
        $this->stars = $stars;
    }

    public function build(QuestionCollection $collection)
    {
        foreach ($this->data as $questionData) {
            $question = $this->createQuestion($this->testID, $questionData, $this->stars);
            $collection->addQuestion($question);
        }
    }

    abstract public function createQuestion(int $testID, $questionData, $stars);

}
