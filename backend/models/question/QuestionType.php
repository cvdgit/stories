<?php

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
}
