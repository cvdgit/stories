<?php

namespace backend\components;

class StoryEditor
{

	protected $story;

	public function __construct(Story $story)
	{
		$this->story = $story;
	}

	public function getSlideMarkup($slideIndex)
	{
        $slide = $this->story->getSlide($slideIndex);
        $html = $slide->render();
        return $html;
	}

	public function setSlideText($slideIndex, $text, $textSize)
	{
        $slide = $this->story->getSlide($slideIndex);
        foreach ($slide->getBlocks() as $block) {
            if (get_class($block) == 'backend\components\SlideBlockText') {
                $block->setText(nl2br($text));
                $block->setTextSize($textSize);
            }
        }
	}

	public function setSlideImage($slideIndex, $imagePath)
	{
        $slide = $this->story->getSlide($slideIndex);
        foreach ($slide->getBlocks() as $block) {
            if (get_class($block) == 'backend\components\SlideBlockImage') {
            	$block->setImageSize($imagePath);
                $block->setImagePath($imagePath);
            }
        }
	}

	public function getSlideValues($slideIndex)
	{
		$slide = $this->story->getSlide($slideIndex);
		$values = ['text' => '', 'image' => ''];
        foreach ($slide->getBlocks() as $block) {
            if (get_class($block) == 'backend\components\SlideBlockText') {
                $values['text'] = preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $block->getText());
                $values['text_size'] = $block->getTextSize();
            }
            if (get_class($block) == 'backend\components\SlideBlockImage') {
                $values['image'] = $block->getImagePath();
            }
        }
        return $values;
	}

	public function getStoryMarkup()
	{
        $html = $this->story->render();
        return $html;
	}

	public function getSlides()
	{
		return $this->story->getSlides();
	}

	public function getStory()
	{
		return $this->story;
	}


	public function updateSlide($slideIndex, $slideText, $slideTextSize, $slideImage = '')
	{
		$this->setSlideText($slideIndex, $slideText, $slideTextSize);
		if (!empty($slideImage)) {
			$this->setSlideImage($slideIndex, $slideImage);
		}
	}

}