<?php

declare(strict_types=1);

namespace backend\components\import\poetry;

use backend\components\import\QuestionDto;
use common\models\TestWord;
use yii\helpers\Json;

class PoetryDragWordsQuestionBuilder
{
    /** @var PoetryPayloadInterface */
    private $payloadBuilder;

    /** @var string */
    private $title;

    public function __construct(string $title, PoetryPayloadInterface $payloadBuilder)
    {
        $this->title = $title;
        $this->payloadBuilder = $payloadBuilder;
    }

    /**
     * @param array{iterable<TestWord>} $words
     * @return iterable<QuestionDto>
     */
    public function createQuestions(array $words): array
    {
        $questions = [];
        foreach ($words as $questionWords) {
            $payload = $this->payloadBuilder->createPayload($questionWords);
            $question = new QuestionDto('Расставьте слова по своим местам', Json::encode($payload));
            foreach ($payload['fragments'] as $fragment) {
                if (!$fragment['correct']) {
                    continue;
                }
                $question->createAnswer($fragment['title'], true);
            }
            $questions[] = $question;
        }
        return $questions;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
