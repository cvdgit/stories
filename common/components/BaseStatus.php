<?php

namespace common\components;

abstract class BaseStatus
{

    abstract public static function asArray(): array;

    public static function asText($status, string $default = 'Unknown status'): string
    {
        $values = static::asArray();
        return $values[$status] ?? $default;
    }

    public static function all(): array
    {
        return array_keys(static::asArray());
    }
}
