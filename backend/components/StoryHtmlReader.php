<?php

namespace backend\components;

class StoryHtmlReader
{

	protected $story;

	public function loadStoryFromHtml($html)
	{
		$this->story = new Story();
		$this->loadSlides($html);
		return $this->story;
	}

	protected function loadSlides($html)
	{
		$document = \phpQuery::newDocumentHTML($html);
        $sections = $document->find('section');
        $slideIndex = 1;
        $slideCount = count($sections);
        foreach ($sections as $section) {
        	$onlyTextLayout = ($slideIndex == 1 || $slideIndex == $slideCount);
            $htmlSlide = pq($section)->htmlOuter();
            $this->loadSlide($htmlSlide, $onlyTextLayout);
            $slideIndex++;
        }
	}

	protected function loadSlide($htmlSlide, $onlyTextLayout)
	{
		$number = $this->story->getSlideCount() + 1;
		
		$slide = $this->story->createSlide();
		$slide->setLayout($onlyTextLayout);
		
		$element = pq($htmlSlide);
		$slideMarkup = new \backend\components\markup\SlideMarkup($slide, $element->get(0)->tagName, $element->attr('*'));
		$slide->setMarkup($slideMarkup);
		
		$slide->setSlideNumber($number);

		$blocks = pq($htmlSlide)->find('div.sl-block');
		$this->loadSlideBlocks($blocks, $slide);
	}

	protected function loadSlideBlocks($htmlBlocks, $slide)
	{
		foreach ($htmlBlocks as $htmlBlock) {
			$blockType = pq($htmlBlock)->attr('data-block-type');
			switch ($blockType) {
				case 'text':
					$this->loadBlockText($htmlBlock, $slide);
					break;
				case 'image':
					$this->loadBlockImage($htmlBlock, $slide);
					break;
				default:
			}
		}
	}

	protected function loadBlockText($htmlBlock, $slide)
	{
		$block = $slide->createBlockText();
        
        if ($slide->getLayout()) {
        	$element = pq($htmlBlock);
            $blockMarkup = new \backend\components\markup\BlockHeaderMarkup($block, $element->get(0)->tagName, $element->attr('*'));

			$element = pq($htmlBlock)->find('div.sl-block-content');
			$blockContentMarkup = new \backend\components\markup\BlockHeaderContentMarkup($block, $element->get(0)->tagName, $element->attr('*'));

			$element = pq($htmlBlock)->find('h1');
			$paragraphMarkup = new \backend\components\markup\HeaderMarkup($block, $element->get(0)->tagName, $element->attr('*'), $element->html());
        }
        else {
        	$element = pq($htmlBlock);
            $blockMarkup = new \backend\components\markup\BlockMarkup($block, $element->get(0)->tagName, $element->attr('*'));
		
			$element = pq($htmlBlock)->find('div.sl-block-content');
			$blockContentMarkup = new \backend\components\markup\BlockContentMarkup($block, $element->get(0)->tagName, $element->attr('*'));

			$element = pq($htmlBlock)->find('p');
			$paragraphMarkup = new \backend\components\markup\ParagraphMarkup($block, $element->get(0)->tagName, $element->attr('*'), $element->html());
        }

		$blockContentMarkup->addElement($paragraphMarkup);
		$blockMarkup->addElement($blockContentMarkup);

		$block->setMarkup($blockMarkup);
	}

	protected function loadBlockImage($htmlBlock, $slide)
	{
		$block = $slide->createBlockImage();
		
        $element = pq($htmlBlock);
		$blockMarkup = new \backend\components\markup\BlockImageMarkup($block, $element->get(0)->tagName, $element->attr('*'));

		$element = pq($htmlBlock)->find('div.sl-block-content');
		$blockContentMarkup = new \backend\components\markup\BlockImageContentMarkup($block, $element->get(0)->tagName, $element->attr('*'));

		$element = pq($htmlBlock)->find('img');
		$imageMarkup = new \backend\components\markup\ImageMarkup($block, $element->get(0)->tagName, $element->attr('*'), $element->attr('data-src'));

		$blockContentMarkup->addElement($imageMarkup);
		$blockMarkup->addElement($blockContentMarkup);

		$block->setMarkup($blockMarkup);
	}

}