<?php

namespace backend\components\book\blocks;

class Text extends Block
{

    /** @var string */
    public $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

}