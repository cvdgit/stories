<?php

namespace backend\models\editor\base;

class BlockAlign
{

    public const LEFT = 1;
    public const RIGHT = 2;
    public const TOP = 3;
    public const BOTTOM = 4;
    public const HORIZONTAL_CENTER = 5;
    public const VERTICAL_CENTER = 6;
    public const SLIDE_CENTER = 7;

    public static function asArray(): array
    {
        return [
            self::LEFT => 'По левому краю',
            self::RIGHT => 'По правому краю',
            self::TOP => 'По верху',
            self::BOTTOM => 'По низу',
            self::HORIZONTAL_CENTER => 'По центру (горизонтально)',
            self::VERTICAL_CENTER => 'По центру (вертикально)',
            self::SLIDE_CENTER => 'По центру слайда',
        ];
    }

}