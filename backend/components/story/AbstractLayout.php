<?php


namespace backend\components\story;


abstract class AbstractLayout
{

    /** @var AbstractBlock[] */
    public $blocks = [];

    public function addBlock(AbstractBlock $block): void
    {
        $this->blocks[] = $block;
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }

}