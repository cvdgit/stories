<?php

declare(strict_types=1);

namespace modules\edu\components;

class ArrayHelper
{
    /**
     * @template TValue
     * @param array<array-key, TValue> $array
     * @param callable(TValue, array-key): bool $callback
     * @return TValue|null
     */
    public static function array_find(array $array, callable $callback)
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        return null;
    }
}
