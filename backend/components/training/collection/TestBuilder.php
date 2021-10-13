<?php

namespace backend\components\training\collection;

use backend\components\training\base\QuestionCollection;
use common\models\StoryTest;

class TestBuilder
{

    private $test;
    private $data;
    private $stars;
    private $collection;

    public function __construct(StoryTest $test, $data, $dataCount, $stars, bool $fastMode = false)
    {
        $this->test = $test;
        $this->data = $data;
        $this->stars = $stars;
        $this->collection = new QuestionCollection($dataCount, $fastMode);
    }

    public function build()
    {
        (new TestCollection($this->data, $this->stars, $this->test))
            ->build($this->collection);
        return $this->collection;
    }

}