<?php

namespace backend\components;

/**
 * Class StoryTextFormatter
 * @package backend\components
 */
class StoryTextFormatter
{

    private $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    private function format()
    {
        $this->text = str_replace('<br>', PHP_EOL, $this->text);

        $texts = explode(PHP_EOL, $this->text);
        $texts = array_filter($texts, static function($word) {
            return trim($word);
        });
        $texts = array_map(static function($word) {
            return trim($word);
        }, $texts);
        $this->text = implode(PHP_EOL, $texts);
    }

    public function formatByProposals()
    {
        $this->text = str_replace('.', "." . PHP_EOL, $this->text);
        $this->text = str_replace('!', "!" . PHP_EOL, $this->text);
        $this->text = str_replace('?', "?" . PHP_EOL, $this->text);
        $this->format();
        return $this->text;
    }

    public function formatByWords()
    {
        $this->text = str_replace(" ", PHP_EOL, $this->text);
        $this->format();
        return $this->text;
    }

}