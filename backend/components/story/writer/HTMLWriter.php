<?php


namespace backend\components\story\writer;


use backend\components\story\Slide;
use backend\components\story\Story;

class HTMLWriter implements WriterInterface
{

    public function renderStory(Story $story): string
    {
        return (new StoryRenderer($story))->render();
    }

    public function renderSlide(Slide $slide): string
    {
        return (new SlideRenderer($slide))->render();
    }

}