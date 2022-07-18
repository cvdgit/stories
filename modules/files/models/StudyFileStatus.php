<?php

namespace modules\files\models;

class StudyFileStatus
{

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public static function asArray(): array
    {
        return [
            self::STATUS_ACTIVE => 'Доступен',
            self::STATUS_INACTIVE => 'Недоступен',
        ];
    }

    public static function asText(int $status): string
    {
        $items = self::asArray();
        return $items[$status] ?? 'Неизвестный статус';
    }
}
