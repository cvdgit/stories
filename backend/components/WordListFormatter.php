<?php

namespace backend\components;

class WordListFormatter
{

    public static function asText($data)
    {
        $texts = [];
        foreach ($data as $item) {
            $texts[] = $item['name'];
        }
        return implode(PHP_EOL, $texts);
    }

}