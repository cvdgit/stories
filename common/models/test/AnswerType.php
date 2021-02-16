<?php

namespace common\models\test;

class AnswerType
{

    public const DEFAULT = 0;
    public const NUMPAD = 1;
    public const INPUT = 2;
    public const RECORDING = 3;
    public const MISSING_WORDS = 4;

    public static function asArray(): array
    {
        return [
            self::DEFAULT => 'По умолчанию',
            self::NUMPAD => 'Цифровая клавиатура',
            self::INPUT => 'Поле для ввода',
            self::RECORDING => 'Запись с микрофона',
            self::MISSING_WORDS => 'Пропущенные слова',
        ];
    }

    public static function asText(int $type): string
    {
        $values = self::asArray();
        return $values[$type];
    }
}