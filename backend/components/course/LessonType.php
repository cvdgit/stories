<?php

namespace backend\components\course;

class LessonType
{

    public const BLOCKS = 1;
    public const QUIZ = 2;

    public static function typeIsBlocks(int $type): bool
    {
        return $type === self::BLOCKS;
    }

    public static function typeIsQuiz(int $type): bool
    {
        return $type === self::QUIZ;
    }
}
