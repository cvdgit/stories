<?php


namespace backend\components\story;


class Story
{

    /** @var Slide[] */
    protected $slides = [];

    public function createSlide(): Slide
    {
        $slide = new Slide();
        $this->addSlide($slide);
        return $slide;
    }

    public function addSlide(Slide $slide): void
    {
        $this->slides[] = $slide;
    }

    public function getSlideCount(): int
    {
        return count($this->slides);
    }

    /**
     * @return Slide[]
     */
    public function getSlides(): array
    {
        return $this->slides;
    }

    public function getSlide($index): Slide
    {
        return $this->slides[$index];
    }

}