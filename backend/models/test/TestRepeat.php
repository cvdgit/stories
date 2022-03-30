<?php

namespace backend\models\test;

class TestRepeat
{

    public const DEFAULT = 5;

    public static function getValues(): array
    {
        return [5, 4, 3, 2, 1];
    }

    public static function getForRange(): array
    {
        return self::getValues();
    }

    public static function getForDropdown(): array
    {
        return array_combine(self::getValues(), self::getValues());
    }
}