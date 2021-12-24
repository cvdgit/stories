<?php

namespace backend\components\training\base;

use common\models\StoryTest;

class Serializer
{

    public function serialize(StoryTest $test,
                              QuestionCollection $collection,
                              $students,
                              int $userStarsCount,
                              bool $fastMode = false,
                              $stories = []): array
    {
        return [
            0 => [
                'storyTestQuestions' => $collection->serialize($test->isShuffleQuestions()),
                'test' => [
                    'id' => $test->id,
                    'progress' => [
                        'total' => $collection->getTotal() * ($fastMode ? 1 : 5),
                        'current' => $userStarsCount,
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
                    'askQuestion' => filter_var($test->ask_question, FILTER_VALIDATE_BOOLEAN),
                    'askQuestionLang' => $test->ask_question_lang,
                    'hideQuestionName' => filter_var($test->hide_question_name, FILTER_VALIDATE_BOOLEAN),
                    'repeatQuestions' => $fastMode ? 1 : 5,
                ],
                'students' => $students,
                'stories' => $stories,
            ],
        ];
    }

}