<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class BlockContentMarkup extends StoryMarkup implements \backend\components\StoryRenderableInterface
{

	protected $defaultMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'class' => 'sl-block-content',
			'data-placeholder-tag' => 'p',
			'data-placeholder-text' => 'Text',
			'style' => 'z-index: 12; text-align: left;',
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

	public function render(): string
	{
		$html = '';
		foreach ($this->elements as $element) {
			$html .= $element->render();
		}
		return $this->getTag($html);
	}

}