<?php

namespace backend\components\story\writer;

use backend\components\story\Story;

class StoryRenderer
{

    private $slideRenderer;

    public function __construct()
    {
        $this->slideRenderer = new SlideRenderer();
    }

    public function render(Story $story): string
    {
        $html = '<div class="slides">';
        foreach ($story->getSlides() as $slide) {
            $html .= $this->slideRenderer->render($slide);
        }
        $html .= '</div>';
        return $html;
    }
}
