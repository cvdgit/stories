<?php

namespace backend\components;

use backend\components\markup\BlockImageMarkup;

class SlideBlockImage extends SlideBlock
{

	protected $img;
	protected $src;

	public function __construct($img = '', $src = '')
	{
		parent::__construct();
		$this->img = $img;
		$this->src = $src;
	}

	public function getImg()
	{
		return $this->img;
	}

	public function setImg($img)
	{
		$this->img = $img;
	}

	public function getSrc()
	{
		return $this->src;
	}

	public function setSrc($src)
	{
		$this->src = $src;
	}

	public function createBlockImageMarkup($new = false)
	{
		$markup = new BlockImageMarkup($this, $new);
		$this->setMarkup($markup);
		return $markup;
	}

}
