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

    public function create(array $texts)
    {
        return array_map(static function($row) {
            @list($text, $correctAnswer) = explode('|', $row);
            $text = trim(preg_replace('/[^\w\-\s.,!?+-]/u', '', $text));
            $correctAnswer = trim(preg_replace('/[^\w\-\s.,]/u', '', $correctAnswer));
            return [
                'name' => $text,
                'correct_answer' => $correctAnswer,
            ];
        }, $texts);
    }

}