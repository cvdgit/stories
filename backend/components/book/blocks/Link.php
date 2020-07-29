<?php

namespace backend\components\book\blocks;

class Link extends Block
{

    public $title;
    public $href;

    public function __construct($title, $href)
    {
        $this->title = $title;
        $this->href = $href;
    }

}