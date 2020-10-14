<?php

namespace backend\components\training\collection;

use backend\components\training\base\QuestionCollection;
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

    private function create(string $className)
    {
        return \Yii::createObject($className, [
            $this->test->id, $this->data, $this->stars
        ]);
    }

    public function build()
    {
        if ($this->test->isAnswerTypeNumPad()) {
            $this->create(NumPadBuilder::class)->build($this->collection);
        }
        else if ($this->test->isAnswerTypeInput()) {
            $this->create(InputBuilder::class)->build($this->collection);
        }
        else if ($this->test->isAnswerTypeRecording()) {
            $this->create(RecordingCollection::class)->build($this->collection);
        }
        else {
            $this->create(CorrectIncorrectBuilder::class)->build($this->collection);
        }
        return $this->collection;
    }

}