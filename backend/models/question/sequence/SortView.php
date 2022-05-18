<?php

namespace backend\models\question\sequence;

class SortView
{

    public const VERTICAL = 0;
    public const HORIZONTAL = 1;

    public static function values(): array
    {
        return [self::VERTICAL, self::HORIZONTAL];
    }

    public static function texts(): array
    {
        return [
            self::VERTICAL => 'Вертикальный',
            self::HORIZONTAL => 'Горизонтальный',
        ];
    }
}
