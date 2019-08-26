<?php


namespace backend\components\story;


use Yii;

class Slide
{

    const VIEW_SLIDE = 'slide';
    const VIEW_QUESTION = 'question';

    public $id;
    public $slideNumber;

    /** @var AbstractBlock[] */
    protected $blocks = [];

    /** @var string */
    protected $view;

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

    public function deleteBlock(string $blockID)
    {
        $blocks = array_filter($this->blocks, function(AbstractBlock $block) use ($blockID) {
            return ($block->getId() !== $blockID);
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

    public function getBlocksArray(): array
    {
        return array_map(function(AbstractBlock $block) {
            return [
                'id' => $block->getId(),
                'type' => $block->getType(),
            ];
        }, $this->blocks);
    }

    /**
     * @param string $blockID
     * @return AbstractBlock
     */
    public function findBlockByID(string $blockID): AbstractBlock
    {
        $blocks = array_filter($this->blocks, function(AbstractBlock $block) use ($blockID) {
            return ($block->getId() === $blockID);
        });
        return array_shift($blocks);
    }

    public function createBlock($type)
    {
        $block = Yii::createObject($type);
        return $block->create();
    }

    /**
     * @return mixed
     */
    public function getSlideNumber()
    {
        return $this->slideNumber;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView(string $view): void
    {
        $this->view = $view;
    }

}