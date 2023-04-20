<?php

declare(strict_types=1);

namespace backend\modules\changelog\models;

class ChangelogStatus
{
    public const DRAFT = 0;
    public const PUBLISH = 1;

    public static function allItems(): array
    {
        return [
            self::DRAFT => 'Черновик',
            self::PUBLISH => 'Опубликовано',
        ];
    }

    public static function getKeys(): array
    {
        return array_keys(self::allItems());
    }
}
