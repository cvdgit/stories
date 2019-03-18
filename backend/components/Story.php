<?php

namespace backend\components;

class Story implements StoryRenderableInterface
{

	protected $slides = [];

	public function __construct()
	{

	}

	public function createSlide()
	{
		$slide = new StorySlide();
		$this->addSlide($slide);
		return $slide;
	}

	public function addSlide(StorySlide $slide)
	{
		$this->slides[] = $slide;
	}

	public function getSlideCount()
	{
		return count($this->slides);
	}

	public function getSlides()
	{
		return $this->slides;
	}

	public function setSlides($slides)
	{
		$this->slides = $slides;
	}

	public function getSlide($index)
	{
		return $this->slides[$index];
	}

	public function getElements(): array {}

	public function render(): string
	{
		$html = '<div class="slides">';
		foreach ($this->slides as $slide) {
            $html .= $slide->render();
        }
		$html .= '</div>';
		return $html;
	}

}