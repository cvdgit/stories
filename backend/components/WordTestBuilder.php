<?php

namespace backend\components;

use backend\components\training\base\Answer;
use backend\components\training\base\QuestionCollection;
use backend\components\training\local\WordQuestion;
use common\models\StoryTest;

class WordTestBuilder
{

    private $test;
    private $data;
    private $stars;
    private $collection;

    public function __construct(StoryTest $test, $data, $dataCount, $stars)
    {
        $this->test = $test;
        $this->data = $data;
        $this->stars = $stars;
        $this->collection = new QuestionCollection($dataCount);
    }

    public function build()
    {

        if ($this->test->isAnswerTypeNumPad()) {
            (new NumPadBuilder($this->test->id, $this->data, $this->stars))->build($this->collection);
        }
        else if ($this->test->isAnswerTypeInput()) {
            (new InputBuilder($this->test->id, $this->data, $this->stars))->build($this->collection);
        }
        else {
            (new CorrectIncorrectBuilder($this->test->id, $this->data, $this->stars))->build($this->collection);
        }

        return $this->collection;
    }

}