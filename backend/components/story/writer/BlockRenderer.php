<?php


namespace backend\components\story\writer;


class BlockRenderer
{

    protected $block;

    public function __construct($block)
    {
        $this->block = $block;
    }

}