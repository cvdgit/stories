<?php

namespace common\models\test;

class SourceType
{

    public const TEST = 1;
    public const NEO = 2;
    public const LIST = 3;
    public const TESTS = 4;

    public static function asArray(): array
    {
        return [
            self::TEST => 'Тест',
            self::LIST => 'Список слов',
            self::NEO => 'Neo4j',
            self::TESTS => 'Итоговый тест',
        ];
    }

    public static function asText(int $source): string
    {
        $values = self::asArray();
        return $values[$source];
    }

    public static function asNavItems(int $source): array
    {
        return array_map(static function(string $value, int $key) use ($source) {
            return [
                'label' => $value,
                'url' => ['test/index', 'source' => $key],
                'active' => ($source === $key),
            ];
        }, self::asArray(), array_keys(self::asArray()));
    }
}
