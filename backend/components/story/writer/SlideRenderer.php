<?php

declare(strict_types=1);

namespace backend\components\story\writer;

use backend\components\story\Slide;

class SlideRenderer
{
    private $blockRenderer;

    public function __construct()
    {
        $this->blockRenderer = new BlockRenderer();
    }

    public function render(Slide $slide): string
    {
        $html = '<section data-id="' . $slide->getId() . '" data-slide-view="' . $slide->getView(
            ) . '" data-audio-src="' . $slide->getAudioFile() . '">';
        foreach ($slide->getBlocks() as $block) {
            $html .= $this->blockRenderer->render($block);
        }
        $html .= '</section>';
        return $html;
    }

    public function renderContent(Slide $slide): string
    {
        $html = '<section data-id="' . $slide->getId() . '" data-slide-view="' . $slide->getView(
            ) . '" data-audio-src="' . $slide->getAudioFile() . '">';
        $html .= $slide->getContent();
        $html .= '</section>';
        return $html;
    }
}
