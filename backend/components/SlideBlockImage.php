<?php

namespace backend\components;

use backend\components\markup\BlockImageMarkup;

class SlideBlockImage extends SlideBlock
{

	protected $imagePath = '';

	public function __construct()
	{
		parent::__construct();
	}

	public function getImagePath()
	{
		$markup = $this->markup;
		while ($elements = $markup->getElements()) {
			$element = $elements[0];
			$markup = $element;
		}
		return $element->getImagePath();
	}

	public function setImagePath($imagePath)
	{
		$markup = $this->markup;
		while ($elements = $markup->getElements()) {
			$element = $elements[0];
			$markup = $element;
		}
		$element->setImagePath($imagePath);
	}

}
