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

    public function __construct(StoryTest $test, $data, $dataCount, $stars, bool $fastMode = false)
    {
        $this->test = $test;
        $this->data = $data;
        $this->stars = $stars;
        $this->collection = new QuestionCollection($dataCount, $fastMode ? 1 : $test->repeat);
    }

    private function create(string $className)
    {
        return \Yii::createObject($className, [
            $this->data, $this->stars
        ]);
    }

    public function build()
    {
        if ($this->test->isAnswerTypeNumPad()) {
            $this->create(NumPadCollection::class)->build($this->collection);
        }
        else if ($this->test->isAnswerTypeInput()) {
            $this->create(InputCollection::class)->build($this->collection);
        }
        else if ($this->test->isAnswerTypeRecording()) {
            /** @var RecordingCollection $collection */
            $collection = $this->create(RecordingCollection::class);
            $collection->setRememberAnswers($this->test->isRememberAnswers());
            $collection->build($this->collection);
        }
        else if ($this->test->isAnswerTypeMissingWords()) {
            $this->create(MissingWordsCollection::class)->build($this->collection);
        }
        else {
            $this->create(CorrectIncorrectCollection::class)->build($this->collection);
        }
        return $this->collection;
    }

}