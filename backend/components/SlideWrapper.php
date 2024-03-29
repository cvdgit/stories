<?php

namespace backend\components;

use backend\components\story\HTMLBLock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\Slide;
use backend\components\story\TestBlock;
use backend\components\story\TestBlockContent;
use backend\components\story\writer\HTMLWriter;

class SlideWrapper
{

    /** @var Slide */
    private $slide;

    public function __construct(string $slideData = '')
    {
        $this->slide = (new HtmlSlideReader($slideData))->load();
    }

    public function findTestId(): ?int
    {
        foreach ($this->slide->getBlocks() as $block) {
            if ($block->isHtmlTest()) {
                /** @var HTMLBLock $block */
                /** @var TestBlockContent $content */
                $content = $block->getContentObject(TestBlockContent::class);
                return $content->getTestID();
            }
        }
        return null;
    }

    public function findTestByActionId(): ?int
    {
        foreach ($this->slide->getBlocks() as $block) {
            if ($block->isTest()) {
                /** @var TestBlock $block */
                return $block->getTestID();
            }
        }
        return null;
    }

    public function getSlideHtml(): string
    {
        return (new HTMLWriter())->renderSlide($this->slide);
    }

    public function getQuizBlock(): ?HTMLBLock
    {
        foreach ($this->slide->getBlocks() as $block) {
            if ($block->isHtmlTest()) {
                /** @var HTMLBLock $block */
                return $block;
            }
        }
        return null;
    }

    public function getQuizBlockId(): ?string
    {
        if (($block = $this->getQuizBlock()) !== null) {
            return $block->getId();
        }
        return null;
    }
}
