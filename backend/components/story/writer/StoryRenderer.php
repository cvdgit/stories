<?php


namespace backend\components\story\writer;


use backend\components\story\Story;

class StoryRenderer
{

    protected $story;

    public function __construct(Story $story)
    {
        $this->story = $story;
    }

    public function getElements(): array {}

    public function render(): string
    {
        $html = '<div class="slides">';
        foreach ($this->story->getSlides() as $slide) {
            $html .= (new SlideRenderer($slide))->render();
        }
        $html .= '</div>';
        return $html;
    }
}