<?php

declare(strict_types=1);

namespace backend\components\story\writer;

use backend\components\story\AbstractBlock;
use backend\components\story\Slide;
use backend\components\story\Story;

class HTMLWriter implements WriterInterface
{
    public function renderStory(Story $story): string
    {
        return (new StoryRenderer())->render($story);
    }

    public function renderSlide(Slide $slide): string
    {
        return (new SlideRenderer())->render($slide);
    }

    public function renderSlideContent(Slide $slide): string
    {
        return (new SlideRenderer())->renderContent($slide);
    }

    public function renderBlock(AbstractBlock $block): string
    {
        return (new BlockRenderer())->render($block);
    }
}
