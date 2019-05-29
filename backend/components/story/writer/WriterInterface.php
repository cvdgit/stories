<?php


namespace backend\components\story\writer;


use backend\components\story\Slide;
use backend\components\story\Story;

interface WriterInterface
{

    public function renderStory(Story $story): string;

    public function renderSlide(Slide $slide): string;

}