<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class ParagraphMarkup extends StoryMarkup implements \backend\components\StoryRenderableInterface
{

	protected $defaultMarkup = [
		'tagName' => 'p',
		'attributes' => [
			'style' => 'color: #FFFFFF; font-size: 0.8em;',
		],
	];

	public function __construct($owner, $tagName = '', $attributes = [], $content = '')
	{
		if (empty($tagName)) {
			$tagName = $this->defaultMarkup['tagName'];
		}
		if (count($attributes) == 0) {
			$attributes = $this->defaultMarkup['attributes'];
		}
		parent::__construct($owner, $tagName, $attributes, $content);
	}

	public function getTextSize()
	{
		return $this->text->getStyleValue('font-size');
	}

	public function setTextSize($textSize)
	{
		$this->text->setStyleValue('font-size', $textSize);
	}

	public function render(): string
	{
		return $this->getTag();
	}

}