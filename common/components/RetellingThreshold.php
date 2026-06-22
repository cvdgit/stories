<?php

declare(strict_types=1);

namespace common\components;

final class RetellingThreshold
{
    public const DEFAULT_THRESHOLD = 90;

    public static function getDefaultThreshold(array $params, string $paramsKey = 'retelling.default.threshold'): int
    {
        $threshold = $params[$paramsKey] ?? null;
        if ($threshold === null) {
            return self::DEFAULT_THRESHOLD;
        }
        $threshold = (int) $threshold;
        if ($threshold === 0) {
            return self::DEFAULT_THRESHOLD;
        }
        return $threshold;
    }

    public static function getThreshold(array $params, int $settingsThreshold = null): int
    {
        return $settingsThreshold ?? self::getDefaultThreshold($params);
    }

    public static function check(int $current, int $threshold): bool
    {
        return $current >= $threshold;
    }
}
