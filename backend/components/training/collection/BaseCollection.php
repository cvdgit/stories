<?php

declare(strict_types=1);

namespace backend\components\training\collection;

use backend\components\training\base\BaseQuestion;
use backend\components\training\base\QuestionCollection;
use common\models\StoryTestQuestion;

abstract class BaseCollection
{
    /**
     * @var list<StoryTestQuestion>
     */
    private $data;
    private $stars;

    public function __construct(array $data, $stars)
    {
        $this->data = $data;
        $this->stars = $stars;
    }

    public function build(QuestionCollection $collection): void
    {
        foreach ($this->data as $questionData) {
            $question = $this->createQuestion($questionData, $this->stars);
            $collection->addQuestion($question);
        }
    }

    abstract public function createQuestion(StoryTestQuestion $questionData, $stars);
}
