<?php

namespace common\models\test;

class TestTemplateParts
{

    public const WORDLIST_NAME = '{WORDLIST}';

    public static function asArray(): array
    {
        return [
            self::WORDLIST_NAME,
        ];
    }

    public static function asText(): string
    {
        return implode(', ', self::asArray());
    }
}
