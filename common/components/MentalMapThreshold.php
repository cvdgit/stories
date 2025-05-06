<?php

declare(strict_types=1);

namespace common\components;

final class MentalMapThreshold
{
    public const DEFAULT_THRESHOLD = 80;

    public static function getDefaultThreshold(array $params): int
    {
        $threshold = $params['mental.map.default.threshold'] ?? null;
        if ($threshold === null) {
            return self::DEFAULT_THRESHOLD;
        }
        $threshold = (int) $threshold;
        if ($threshold === 0) {
            return self::DEFAULT_THRESHOLD;
        }
        return $threshold;
    }

    public static function getMentalMapThreshold(array $payload): ?int
    {
        $settings = $payload['settings'] ?? null;
        if ($settings === null) {
            return null;
        }
        $threshold = $settings['threshold'] ?? null;
        if ($threshold === null) {
            return null;
        }
        $threshold = (int) $threshold;
        if ($threshold > 0) {
            return $threshold;
        }
        return null;
    }

    public static function getThreshold(array $params, array $payload): int
    {
        $threshold = self::getMentalMapThreshold($payload);
        if ($threshold === null) {
            $threshold = self::getDefaultThreshold($params);
        }
        return $threshold;
    }
}
