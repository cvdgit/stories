<?php

namespace backend\components;


use backend\components\markup\ButtonMarkup;

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
		$markup = $this->markup;
		while ($elements = $markup->getElements()) {
			$element = $elements[0];
			$markup = $element;
		}
		return $element->getContent();
	}

	public function setText($text)
	{
		$markup = $this->markup;
		while ($elements = $markup->getElements()) {
			$element = $elements[0];
			$markup = $element;
		}
		$element->setContent($text);
	}

	public function getTextSize()
	{
		$markup = $this->markup;
		while ($elements = $markup->getElements()) {
			$element = $elements[0];
			$markup = $element;
		}
		return $element->getStyleValue('font-size');
	}

	public function setTextSize($textSize)
	{
		$markup = $this->markup;
		while ($elements = $markup->getElements()) {
			$element = $elements[0];
			$markup = $element;
		}
		$element->setStyleValue('font-size', $textSize);
	}

	public function setButton($slideButton)
    {
        $elements = $this->markup->getElements();
        $element = $elements[0];
        $buttonMarkup = new ButtonMarkup($element->getBlock());
        $buttonMarkup->setContent('KNOPKA');
        $element->addElement($buttonMarkup);
    }

}
