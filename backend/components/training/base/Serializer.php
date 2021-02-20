<?php

namespace backend\components\training\base;

use common\models\StoryTest;

class Serializer
{

    public function serialize(StoryTest $test, QuestionCollection $collection, $students, $userStarsCount, $stories = []): array
    {
        return [
            0 => [
                'storyTestQuestions' => $collection->serialize($test->isShuffleQuestions()),
                'test' => [
                    'id' => $test->id,
                    'progress' => [
                        'total' => $collection->getTotal() * 5,
                        'current' => (int) $userStarsCount,
                    ],
                    'showAnswerImage' => true,
                    'showAnswerText' => true,
                    'showQuestionImage' => true,
                    'source' => $test->source,
                    'answerType' => $test->answer_type,
                    'strictAnswer' => $test->strict_answer,
                    'inputVoice' => $test->input_voice,
                    'recordingLang' => $test->recording_lang,
                    'rememberAnswers' => filter_var($test->remember_answers, FILTER_VALIDATE_BOOLEAN),
                ],
                'students' => $students,
                'stories' => $stories,
            ],
        ];
    }

}