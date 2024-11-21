<?php

declare(strict_types=1);

namespace backend\components\training\collection;

use backend\components\training\base\QuestionCollection;
use common\models\StoryTest;
use common\models\StoryTestQuestion;

class TestBuilder
{
    private $test;
    /**
     * @var list<StoryTestQuestion>
     */
    private $data;
    private $stars;
    private $collection;

    public function __construct(StoryTest $test, array $data, int $dataCount, array $stars, bool $fastMode = false)
    {
        $this->test = $test;
        $this->data = $data;
        $this->stars = $stars;
        $this->collection = new QuestionCollection($dataCount, $fastMode ? 1 : $test->repeat);
    }

    public function build(): QuestionCollection
    {
        (new TestCollection($this->data, $this->stars, $this->test))
            ->build($this->collection);
        return $this->collection;
    }
}
