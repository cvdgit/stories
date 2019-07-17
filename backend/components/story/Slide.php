<?php


namespace backend\components\story;


class Slide
{

    public $id;
    public $slideNumber;

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

    public function deleteBlock(string $targetBlockID)
    {
        $blocks = array_filter($this->blocks, function(AbstractBlock $block) use ($targetBlockID) {
            return ($block->getId() !== $targetBlockID);
        });
        $this->blocks = $blocks;
    }

    /**
     * @param mixed $slideNumber
     */
    public function setSlideNumber($slideNumber): void
    {
        $this->slideNumber = $slideNumber;
    }

}