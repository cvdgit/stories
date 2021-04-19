<?php

namespace common\models\story;

class StoryStatus
{

    public const DRAFT = 0;
    public const PUBLISHED = 1;
    public const FOR_PUBLICATION = 2;

    public static function asArray(): array
    {
        return [
            self::DRAFT => 'Черновик',
            self::PUBLISHED => 'Опубликован',
            self::FOR_PUBLICATION => 'На публикацию',
        ];
    }

    public static function asText(string $status): string
    {
        $values = self::asArray();
        return $values[$status] ?? 'Unknown status';
    }
}
