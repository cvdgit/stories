<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class ImageMarkup extends StoryMarkup implements \backend\components\StoryRenderableInterface
{

	protected $defaultMarkup = [
		'tagName' => 'img',
		'attributes' => [
			'data-src' => '',
			'data-natural-width' => 1459,
			'data-natural-height' => 1080,
		],
	];

	const DEFAULT_IMAGE_WIDTH = 973;
	const DEFAULT_IMAGE_HEIGHT = 720;

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

	public function setImagePath($imagePath)
	{
		$this->setAttribute('data-src', $imagePath);
	}

	public function getImagePath()
	{
		return $this->getAttribute('data-src');
	}

	public function render(): string
	{
		return $this->getTag();
	}


}