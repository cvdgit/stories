<?php

namespace backend\components;

use yii\helpers\Html;

class StoryMarkup
{

	protected $tagName;
	protected $attributes;
	protected $content;

	protected $contentMarkup;

	protected $style = [];

	public function __construct($tagName, $attributes, $content = '')
	{
		$this->tagName = $tagName;
		$this->attributes = $attributes;
		$this->content = $content;
	}

	public function init(StoryMarkup $markup)
	{
		$this->tagName = $markup->tagName;
		$this->attributes = $markup->attributes;
		$this->content = $markup->content;
		$this->contentMarkup  = $markup->getContentMarkup();
	}

	public function getTagName()
	{
		return $this->tagName;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getTag($content = '')
	{
		return Html::tag($this->tagName, $content, $this->attributes);
	}

	public function setContentMarkup($markup)
	{
		$this->contentMarkup = $markup;
	}

	public function getContentMarkup()
	{
		return $this->contentMarkup;
	}

	public function getWidth()
	{
		return $this->getStyleValue('width');
	}

	public function setWidth($width)
	{
		$this->setStyleValue('width', $width);
	}

	public function getHeight()
	{
		return $this->getStyleValue('height');
	}

	public function setHeight($height)
	{
		$this->setStyleValue('height', $height);
	}

	public function setTop($top)
	{
		$this->setStyleValue('top', $top);
	}

	public function setLeft($left)
	{
		$this->setStyleValue('left', $left);
	}

	protected function styleToArray($style)
	{
		$styleArray = [];
		foreach (explode(';', $style) as $part) {
			if (!empty($part)) {
				list($paramName, $paramValue) = explode(':', $part);
				$styleArray[trim($paramName)] = trim($paramValue);
			}
		}
		return $styleArray;
	}

	protected function arrayToStyle($styleArray)
	{
		$style = '';
		foreach ($styleArray as $param => $value) {
			$style .= "{$param}: {$value};";
		}
		return $style;
	}

	public function setStyleValue($param, $value)
	{
		if (isset($this->attributes['style'])) {
			$styleArray = $this->styleToArray($this->attributes['style']);
			$styleArray[$param] = $value;
			$this->attributes['style'] = $this->arrayToStyle($styleArray);
		}
	}

	public function getStyleValue($param)
	{
		$value = '';
		if (isset($this->attributes['style'])) {
			$styleArray = $this->styleToArray($this->attributes['style']);
			$value = isset($styleArray[$param]) ? $styleArray[$param] : '';
		}
		return $value;
	}

}
