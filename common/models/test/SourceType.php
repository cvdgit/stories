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
            self::NEO => 'Neo4j',
            self::LIST => 'Список слов',
        ];
    }

    public static function asText(int $source)
    {
        $values = self::asArray();
        return $values[$source];
    }

}