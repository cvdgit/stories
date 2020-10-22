<?php

namespace backend\components;

class WordListFormatter
{

    public static function asText($data)
    {
        $texts = [];
        foreach ($data as $item) {
            $text = $item['name'];
            if (!empty($item['correct_answer'])) {
                $text .= '|' . $item['correct_answer'];
            }
            $texts[] = $text;
        }
        return implode(PHP_EOL, $texts);
    }

}