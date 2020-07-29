<?php

namespace backend\components\book\blocks;

class Image extends Block
{

    /** @var string */
    public $image;

    public function __construct($image)
    {
        $this->image = $image;
    }

}