<?php

namespace backend\components;

use Yii;
use backend\components\markup\SlideMarkup;

class StorySlide implements StoryRenderableInterface
{

	protected $slideNumber;
	protected $id;

	protected $blocks = [];

	protected $markup;

	protected $layout;

	public function __construct()
	{
		$this->id = Yii::$app->security->generateRandomString();
	}

	public function getId()
	{
		return $this->id;
	}

	public function getBlocks()
	{
		return $this->blocks;
	}

	public function addBlock(SlideBlock $block)
	{
		$this->blocks[] = $block;
	}

	public function getSlideNumber()
	{
		return $this->slideNumber;
	}

	public function setSlideNumber($number)
	{
		$this->slideNumber = $number;
	}

	public function createBlockText($text = '')
	{
		$slideBlockText = new SlideBlockText($text);
		$this->addBlock($slideBlockText);
		return $slideBlockText;
	}

	public function createBlockImage($img = '', $src = '')
	{
		$slideBlockImage = new SlideBlockImage($img, $src);
		$this->addBlock($slideBlockImage);
		return $slideBlockImage;
	}

	public function setMarkup($markup)
	{
		$this->markup = $markup;
	}

	public function getMarkup()
	{
		return $this->markup;
	}

	public function setSlideMarkup()
	{
		$markup = new SlideMarkup($this);
		$this->setMarkup($markup);
		return $markup;
	}

	public function setLayout($onlyTextLayout)
	{
		$this->layout = $onlyTextLayout;
	}

	public function getLayout()
	{
		return $this->layout;
	}

	public function getElements(): array {}

	public function render(): string
	{
		$html = '';
		foreach ($this->blocks as $block) {
			$html .= $block->render();
		}
		return $this->markup->getTag($html);
	}

}