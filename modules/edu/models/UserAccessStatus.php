<?php

declare(strict_types=1);

namespace modules\edu\models;

class UserAccessStatus
{

    public const INACTIVE = 0;
    public const ACTIVE = 1;

    public static function asArray(): array
    {
        return [
            self::INACTIVE => 'Закрытый',
            self::ACTIVE => 'Действующий',
        ];
    }

    public static function asRange(): array
    {
        return array_keys(self::asArray());
    }
}
