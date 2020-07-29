<?php

namespace backend\components\book\blocks;

class Transition extends Block
{

    public $title;

    public function __construct($title)
    {
        $this->title = $title;
    }

}