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
		$slide->setMarkup($this->getMarkup($htmlSlide));
		
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
            $blockMarkup = $block->createBlockHeaderMarkup();
        }
        else {
            $blockMarkup = $block->createBlockTextMarkup();
        }

		$textElement = pq($htmlBlock)->find('div.sl-block-content');
		$block->setText($textElement->text());

		$markup = $this->getMarkup($htmlBlock, $textElement->html());
		$markup->setContentMarkup($this->getMarkup(pq($htmlBlock)->find('div.sl-block-content')->htmlOuter()));
		
		$blockMarkup->init($markup);
		$blockMarkup->setText($textElement->text());
	}

	protected function loadBlockImage($htmlBlock, $slide)
	{
		$block = $slide->createBlockImage();
		$blockMarkup = $block->createBlockImageMarkup();

		$markup = $this->getMarkup($htmlBlock);
		$markup->setContentMarkup($this->getMarkup(pq($htmlBlock)->find('div.sl-block-content')->htmlOuter()));
		
		$img = pq($htmlBlock)->find('img')->htmlOuter();
		$src = pq($htmlBlock)->find('img')->attr('data-src');
		
		$block->setImg($img);
		$block->setSrc($src);

		$blockMarkup->init($markup);
		$blockMarkup->setImage($src);
	}

	protected function getMarkup($html, $content = '')
	{
		$element = pq($html);
		return new StoryMarkup(
			$element->get(0)->tagName,
			$element->attr('*'),
			$content
		);
	}

}