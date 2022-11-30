<?php

declare(strict_types=1);

namespace backend\components\training\base;

use common\models\StoryTest;
use yii\helpers\HtmlPurifier;

class Serializer
{
    private $historyValues;

    public function __construct(array $historyValues = [])
    {
        $this->historyValues = $historyValues;
    }

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
                    'description' => HtmlPurifier::process(nl2br($test->description_text ?? '')),
                    'progress' => [
                        'total' => $collection->getTotal() * ($fastMode ? 1 : $test->repeat),
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
                    'hideAnswersName' => filter_var($test->hide_answers_name, FILTER_VALIDATE_BOOLEAN),
                    'repeatQuestions' => $fastMode ? 1 : $test->repeat,
                    'sayCorrectAnswer' => filter_var($test->say_correct_answer, FILTER_VALIDATE_BOOLEAN),
                    'voiceResponse' => filter_var($test->voice_response, FILTER_VALIDATE_BOOLEAN),
                    'showDescriptionInQuestions' => filter_var($test->show_descr_in_questions, FILTER_VALIDATE_BOOLEAN),
                ],
                'students' => $students,
                'stories' => $stories,
                'historyValues' => $this->historyValues,
            ],
        ];
    }
}
