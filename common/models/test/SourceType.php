<?php

namespace common\models\test;

class SourceType
{

    public const TEST = 1;
    public const NEO = 2;
    public const LIST = 3;

    public static function asArray()
    {
        return [
            self::TEST => 'Тест',
            self::LIST => 'Список слов',
            self::NEO => 'Neo4j',
        ];
    }

    public static function asText(int $source)
    {
        $values = self::asArray();
        return $values[$source];
    }

    public static function asNavItems(int $source)
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
