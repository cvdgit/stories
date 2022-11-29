<?php

namespace backend\components;

use JetBrains\PhpStorm\ArrayShape;

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

    /**
     * @return array{name: string, correct_answer: string}
     */
    private function createWord(string $name, $correct = ''): array
    {
        $name = trim(preg_replace('/\s{2,}/', ' ', $name));
        $correct = trim(preg_replace('/\s{2,}/', ' ', $correct));
        $correct = trim(preg_replace('/[^\w\-\s.?!;,#]/u', '', $correct));
        return [
            'name' => $name,
            'correct_answer' => $correct,
        ];
    }

    public function create(array $texts): array
    {
        $texts = array_filter($texts, function($value) {
            if (strpos($value, '|') !== false) {
                $value = explode('|', $value)[0];
            }
            return !is_null($value) && $value !== '';
        });
        return array_map(function($row) {
            @list($text, $correctAnswer) = explode('|', $row);
            return $this->createWord($text, $correctAnswer);
        }, $texts);
    }

    public function haveMatches(string $text, &$matches)
    {
        return preg_match_all('/(\\d+)#([\\w]+)/ui', $text, $matches);
    }

    /**
     * @return array{name: string, correct_answer: string}
     */
    public function createOne(string $name, string $correct = ''): array
    {
        return $this->createWord($name, $correct);
    }

    public static function stringAsWords(string $string, string $separator = ' '): array
    {
        $string = trim(preg_replace('/\s{2,}/', ' ', $string));
        $result = explode($separator, $string);
        if ($result === false) {
            return [];
        }
        return $result;
    }
}
