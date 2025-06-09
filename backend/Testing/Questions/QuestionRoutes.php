<?php

declare(strict_types=1);

namespace backend\Testing\Questions;

use backend\models\question\QuestionType;

class QuestionRoutes
{
    public static function getRoutes(int $quizId): array
    {
        return [
            'По умолчанию' => self::getCreateQuestionRoute($quizId),
            'Выбор области' => self::getCreateRegionQuestionRoute($quizId),
            'Последовательность' => self::getCreateSequenceQuestionRoute($quizId),
            'Тест с пропусками' => self::getCreatePassTestQuestionRoute($quizId),
            'Перетаскивание слов' => self::getCreateDragWordsQuestionRoute($quizId),
            'Изображение с пропусками' => self::getCreateImageGapsQuestionRoute($quizId),
            'Группировка элементов' => ['test/grouping/create', 'test_id' => $quizId],
            'ChatGPT вопрос' => ['test/gpt/create', 'test_id' => $quizId],
            'Математические формулы' => ['test/math/create', 'test_id' => $quizId],
        ];
    }

    public static function getCreateQuestionRoute(int $quizId): array
    {
        return ['test/create-question', 'test_id' => $quizId];
    }

    public static function getCreateRegionQuestionRoute(int $quizId): array
    {
        return ['question/create', 'test_id' => $quizId, 'type' => QuestionType::REGION];
    }

    public static function getCreateSequenceQuestionRoute(int $quizId): array
    {
        return ['test/question-sequence/create', 'test_id' => $quizId];
    }

    public static function getCreatePassTestQuestionRoute(int $quizId): array
    {
        return ['test/pass-test/create', 'test_id' => $quizId];
    }

    public static function getCreateDragWordsQuestionRoute(int $quizId): array
    {
        return ['test/drag-words/create', 'test_id' => $quizId];
    }

    public static function getCreateImageGapsQuestionRoute(int $quizId): array
    {
        return ['test/image-gaps/create', 'test_id' => $quizId];
    }
}
