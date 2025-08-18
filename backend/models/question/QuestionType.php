<?php

declare(strict_types=1);

namespace backend\models\question;

use DomainException;

class QuestionType
{
    public const ONE = 0;
    public const MANY = 1;
    public const REGION = 2;
    public const SEQUENCE = 3;
    public const ANSWER_NUMPAD = 4;
    public const ANSWER_INPUT = 5;
    public const ANSWER_RECORDING = 6;
    public const ANSWER_MISSING_WORDS = 7;
    public const PASS_TEST = 8;
    public const DRAG_WORDS = 9;
    public const POETRY = 10;
    public const IMAGE_GAPS = 11;
    public const GROUPING = 12;
    public const GPT_QUESTION = 13;
    public const MATH_QUESTION = 14;
    public const STEP_QUESTION = 15;

    private $type;

    public function __construct(int $type)
    {
        $types = self::asArray();
        if (!isset($types[$type])) {
            throw new DomainException('Unknown type');
        }
        $this->type = $type;
    }

    public static function asArray(): array
    {
        return [
            self::ONE => 'Один ответ',
            self::MANY => 'Множественный выбор',
            self::REGION => 'Выбор области',
            self::SEQUENCE => 'Последовательность',
            self::ANSWER_NUMPAD => 'Цифровая клавиатура',
            self::ANSWER_INPUT => 'Поле для ввода',
            self::ANSWER_RECORDING => 'Запись с микрофона',
            self::ANSWER_MISSING_WORDS => 'Пропущенные слова',
            self::PASS_TEST => 'Тест с пропусками',
            self::DRAG_WORDS => 'Перетаскивание слов',
            self::POETRY => 'Запоминание стихов',
            self::IMAGE_GAPS => 'Изображение с пропусками',
            self::GROUPING => 'Группировка элементов',
            self::GPT_QUESTION => 'ChatGPT вопрос',
            self::MATH_QUESTION => 'Математические формулы',
            self::STEP_QUESTION => 'Ступенчатый вопрос',
        ];
    }

    public function isRegion(): bool
    {
        return $this->type === self::REGION;
    }

    public function isSequence(): bool
    {
        return $this->type === self::SEQUENCE;
    }

    public function isSingle(): bool
    {
        return $this->type === self::ONE;
    }

    public function isMultiple(): bool
    {
        return $this->type === self::MANY;
    }

    public function getTypeName(): string
    {
        return self::asArray()[$this->type];
    }

    public function isPassTest(): bool
    {
        return $this->type === self::PASS_TEST;
    }

    public function isDragWords(): bool
    {
        return $this->type === self::DRAG_WORDS;
    }

    public function isPoetry(): bool
    {
        return $this->type === self::POETRY;
    }

    public function isImageGaps(): bool
    {
        return $this->type === self::IMAGE_GAPS;
    }

    public function isGrouping(): bool
    {
        return $this->type === self::GROUPING;
    }

    public function isGptQuestion(): bool
    {
        return $this->type === self::GPT_QUESTION;
    }

    public function isMathQuestion(): bool
    {
        return $this->type === self::MATH_QUESTION;
    }

    public function isStepQuestion(): bool
    {
        return $this->type === self::STEP_QUESTION;
    }
}
