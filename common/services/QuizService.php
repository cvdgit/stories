<?php

namespace common\services;

use backend\components\training\base\Serializer;
use backend\components\training\collection\TestBuilder;
use common\models\StoryTest;

class QuizService
{

    public function getQuizData(StoryTest $test): array
    {
        $collection = (new TestBuilder($test, $test->getQuestionData(), $test->getQuestionDataCount(), [], true))
            ->build();
        return (new Serializer())
            ->serialize($test, $collection, [], 0, true);
    }
}