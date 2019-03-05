<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class BlockImageMarkup extends StoryMarkup
{

	protected $defaultMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'data-block-id' => '',
			'class' => 'sl-block',
			'data-block-type' => 'image',
			'style' => 'min-width: 4px; min-height: 4px; width: 973px; height: 720px; left: 0px; top: 0px;',
		],
	];
	protected $defaultContentMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'class' => 'sl-block-content',
			'style' => 'z-index: 11;',
		],
	];

	protected $image;

	const DEFAULT_IMAGE_WIDTH = 973;
	const DEFAULT_IMAGE_HEIGHT = 720;

	public function __construct($owner, $new = false)
	{
		if ($new) {
			parent::__construct($this->defaultMarkup['tagName'], $this->defaultMarkup['attributes']);
			$contentMarkup = new StoryMarkup($this->defaultContentMarkup['tagName'], $this->defaultContentMarkup['attributes']);
			$this->setContentMarkup($contentMarkup);
		}
	}

	public function setImage($imagePath)
	{
		$this->image = new StoryMarkup('img', [
			'data-src' => $imagePath,
			'data-natural-width' => 1459,
			'data-natural-height' => 1080,
		]);
	}

	public function getImage()
	{
		return $this->image;
	}

}