<?php

namespace common\models\slide;

class SlideKind
{

    public const SLIDE = 0;
    public const LINK = 1;
    public const QUESTION = 2;
    public const FINAL_SLIDE = 3;

    public static function isLink($slide): bool
    {
        return (int)$slide->kind === self::LINK;
    }
}
