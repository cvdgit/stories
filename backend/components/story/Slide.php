<?php


namespace backend\components\story;


class Slide
{

    /** @var AbstractLayout */
    protected $layout;

    /** @var AbstractBlock[] */
    protected $blocks = [];

    /**
     * @return AbstractBlock[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function setBlocks($blocks): void
    {
        $this->blocks = $blocks;
    }

    public function addBlock(AbstractBlock $block): void
    {
        $this->blocks[] = $block;
    }

    public function setLayout(AbstractLayout $layout): void
    {
        $this->layout = $layout;
        $this->blocks = $layout->getBlocks();
    }

    public function getLayout(): AbstractLayout
    {
        return $this->layout;
    }

}