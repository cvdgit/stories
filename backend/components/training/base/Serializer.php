<?php

namespace backend\components\training\base;

use common\models\StoryTest;

class Serializer
{

    public function serialize(StoryTest $test, QuestionCollection $collection, $students, $userStarsCount): array
    {
        return [
            0 => [
                'storyTestQuestions' => $collection->serialize(),
                'test' => [
                    'progress' => [
                        'total' => $collection->getTotal() * 5,
                        'current' => (int) $userStarsCount,
                    ],
                    'showAnswerImage' => true,
                    'showAnswerText' => true,
                    'showQuestionImage' => true,
                    'source' => $test->source,
                ],
                'students' => $students,
            ],
        ];
    }

}