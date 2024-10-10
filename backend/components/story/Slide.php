<?php

namespace backend\components\story;

use Yii;
use yii\base\InvalidConfigException;

class Slide
{
    const VIEW_SLIDE = 'slide';
    const VIEW_QUESTION = 'question';
    const VIEW_NEWQUESTION = 'new-question';

    public $id;
    public $slideNumber;

    /** @var AbstractBlock[] */
    protected $blocks = [];

    /** @var string */
    protected $view;

    /** @var string */
    protected $audioFile;

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
        $block = $this->findBlockByID($blockID);
        $block->delete();
        $blocks = array_filter($this->blocks, function(AbstractBlock $block) use ($blockID) {
            return ($block->getId() !== $blockID);
        });
        $this->blocks = $blocks;
    }

    /**
     * @param mixed $slideNumber
     */
    public function setSlideNumber(int $slideNumber): void
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
        $blocks = array_filter($this->blocks, static function(AbstractBlock $block) use ($blockID) {
            return ($block->getId() === $blockID);
        });
        return array_shift($blocks);
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @throws InvalidConfigException
     * @return T
     */
    public function createBlock(string $type): object
    {
        return Yii::createObject($type)->create();
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

    /**
     * @return string
     */
    public function getAudioFile()
    {
        return $this->audioFile;
    }

    /**
     * @param string $audioFile
     */
    public function setAudioFile(string $audioFile): void
    {
        $this->audioFile = $audioFile;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return list<VideoBlock>
     */
    public function getVideoBlocks(): array
    {
        return array_filter($this->blocks, static function(AbstractBlock $block) {
            return $block->isVideo();
        });
    }
}
