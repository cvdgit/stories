<?php

namespace backend\components;

use backend\components\markup\BlockTextMarkup;
use backend\components\markup\BlockHeaderMarkup;

class SlideBlockText extends SlideBlock
{

	protected $text;

	public function __construct($text)
	{
		parent::__construct();
		$this->text = $text;
	}

	public function getText()
	{
		return $this->text;
	}

	public function setText($text)
	{
		$this->text = $text;
	}

	public function createBlockTextMarkup($new = false)
	{
		$markup = new BlockTextMarkup($this, $new);
		$this->setMarkup($markup);
		return $markup;
	}

	public function createBlockHeaderMarkup($new = false)
	{
		$markup = new BlockHeaderMarkup($this, $new);
		$this->setMarkup($markup);
		return $markup;
	}

}
