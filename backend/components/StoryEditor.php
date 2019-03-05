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
        $html = '';
        foreach ($slide->getBlocks() as $block) {
            $content = '';
            if (get_class($block) == 'backend\components\SlideBlockText') {
                $content = $block->getMarkup()->getContent();
            }
            if (get_class($block) == 'backend\components\SlideBlockImage') {
                $content = $block->getImg();
            }
            $markup = $block->getMarkup();
            $contentMarkup = $markup->getContentMarkup();
            $html .= $markup->getTag($contentMarkup->getTag($content));
        }
        return $slide->getMarkup()->getTag($html);
	}

	public function setSlideText($slideIndex, $text)
	{
        $slide = $this->story->getSlide($slideIndex);
        foreach ($slide->getBlocks() as $block) {
            if (get_class($block) == 'backend\components\SlideBlockText') {
                $block->setText($text);
                $block->getMarkup()->setText($text);
            }
        }
	}

	public function getSlideValues($slideIndex)
	{
		$slide = $this->story->getSlide($slideIndex);
		$values = ['text' => '', 'image' => ''];
        foreach ($slide->getBlocks() as $block) {
            if (get_class($block) == 'backend\components\SlideBlockText') {
                $values['text'] = $block->getText();
            }
            if (get_class($block) == 'backend\components\SlideBlockImage') {
                $values['image'] = $block->getImg();
            }
        }
        return $values;
	}

	public function getStoryMarkup()
	{

/*
            $html = '';
            foreach ($slide->getBlocks() as $block) {
                $content = '';
                if (get_class($block) == 'backend\components\SlideBlockText') {
                    $content = $block->getText();
                }
                if (get_class($block) == 'backend\components\SlideBlockImage') {
                    $content = $block->getImg();
                }
                $markup = $block->getMarkup();
                $contentMarkup = $markup->getContentMarkup();

                $html .= $markup->getTag($contentMarkup->getTag($content));
            }

            $response = $slide->getMarkup()->getTag($html);
*/


		$html = '';
		$slides = $this->story->getSlides();
		
		foreach ($slides as $slide) {

	        $blocksMarkup = '';
	        foreach ($slide->getBlocks() as $block) {
	            
	            $markup = $block->getMarkup();
	            $contentMarkup = $markup->getContentMarkup();

	            if (get_class($block) == 'backend\components\SlideBlockText') {
	                $content = $markup->getText()->getTag($markup->getText()->getContent());
	            }

	            if (get_class($block) == 'backend\components\SlideBlockImage') {
	                $content = $markup->getImage()->getTag();
	            }

	            $blocksMarkup .= $markup->getTag($contentMarkup->getTag($content));
	        }

	        $html .= $slide->getMarkup()->getTag($blocksMarkup);
	    }

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

}