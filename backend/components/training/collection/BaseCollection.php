<?php

namespace backend\components\training\collection;

use backend\components\training\base\QuestionCollection;

abstract class BaseCollection
{

    private $data;
    private $stars;

    public function __construct($data, $stars)
    {
        $this->data = $data;
        $this->stars = $stars;
    }

    public function build(QuestionCollection $collection)
    {
        foreach ($this->data as $questionData) {
            $question = $this->createQuestion($questionData, $this->stars);
            $collection->addQuestion($question);
        }
    }

    abstract public function createQuestion($questionData, $stars);
}
