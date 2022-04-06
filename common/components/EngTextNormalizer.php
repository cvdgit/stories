<?php

namespace common\components;

class EngTextNormalizer
{

    public static function normalize(string $text): string
    {
        $abc = [
            'А' => 'A',
            'а' => 'a',
            'ь' => 'b',
            'В' => 'B',
            'г' => 'r',
            'С' => 'C',
            'с' => 'c',
            'Е' => 'E',
            'е' => 'e',
            'Т' => 'T',
            'т' => 't',
            'Н' => 'H',
            'п' => 'n',
            'О' => 'O',
            'о' => 'o',
            'Р' => 'P',
            'р' => 'p',
            'К' => 'K',
            'к' => 'k',
            'Х' => 'X',
            'х' => 'x',
            'М' => 'M',
            'м' => 'm',
            'У' => 'Y',
            'у' => 'y',
            '’' => "'",
            '“' => '"',
            '”' => '"',
        ];
        $matches = [];
        if (preg_match_all('/[А-я’“”]/u', $text, $matches, PREG_PATTERN_ORDER)) {
            if (isset($matches[0])) {
                foreach ($matches[0] as $char) {
                    if (isset($abc[$char])) {
                        $text = str_replace($char, $abc[$char], $text);
                    }
                }
            }
        }
        return $text;
    }
}