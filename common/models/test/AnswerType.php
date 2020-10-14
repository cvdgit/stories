<?php


namespace common\models\test;


class AnswerType
{

    public const DEFAULT = 0;
    public const NUMPAD = 1;
    public const INPUT = 2;
    public const RECORDING = 3;

    public static function asArray()
    {
        return [
            self::DEFAULT => 'По умолчанию',
            self::NUMPAD => 'Цифровая клавиатура',
            self::INPUT => 'Поле для ввода',
            self::RECORDING => 'Запись с микрофона',
        ];
    }

}