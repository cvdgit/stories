<?php

namespace backend\components;

use yii\helpers\Html;

class StoryMarkup
{

	protected $block;

	protected $tagName;
	protected $attributes;
	protected $content;

	/** @var StoryMarkup[] */
	protected $elements = [];

	protected $style = [];

	public function __construct($block, $tagName, $attributes, $content = '')
	{
		$this->block = $block;

		$this->tagName = $tagName;
		$this->attributes = $attributes;
		$this->content = $content;
	}

	public function setBlock($block)
	{
		$this->block = $block;
	}

	public function getBlock()
	{
		return $this->block;
	}

	public function getTagName()
	{
		return $this->tagName;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function setAttributes($attributes): void
	{
		$this->attributes = $attributes;
	}

	public function setAttribute($name, $value)
	{
		if (isset($this->attributes[$name])) {
			$this->attributes[$name] = $value;
		}
	}

	public function getAttribute($name): string
	{
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
		return '';
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getTag($content = '')
	{
		if (empty($content)) {
			$content = $this->content;
		}
		return Html::tag($this->tagName, $content, $this->attributes);
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

	public function getStyleValue($param): string
	{
		$value = '';
		if (isset($this->attributes['style'])) {
			$styleArray = $this->styleToArray($this->attributes['style']);
			$value = isset($styleArray[$param]) ? $styleArray[$param] : '';
		}
		return $value;
	}

	public function addElement(StoryMarkup $element)
	{
		$this->elements[] = $element;
	}

	public function getElements(): array
	{
		return $this->elements;
	}

}
