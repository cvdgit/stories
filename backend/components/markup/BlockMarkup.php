<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class BlockMarkup extends StoryMarkup implements \backend\components\StoryRenderableInterface
{
	
	protected $defaultMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'data-block-id' => '',
			'class' => 'sl-block',
			'data-block-type' => 'text',
			'style' => 'height: auto; min-width: 30px; min-height: 30px; width: 290px; left: 983px; top: 9px;',
		],
	];

	public function __construct($owner, $tagName = '', $attributes = [], $content = '')
	{
		if (empty($tagName)) {
			$tagName = $this->defaultMarkup['tagName'];
		}
		if (count($attributes) == 0) {
			$attributes = $this->defaultMarkup['attributes'];
			$attributes['data-block-id'] = $owner->getId();
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