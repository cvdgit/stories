<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;
use backend\components\StoryRenderableInterface;

class SlideMarkup extends StoryMarkup implements StoryRenderableInterface
{

	protected $defaultMarkup = [
		'tagName' => 'section',
		'attributes' => [
			'data-id' => '',
			'data-background-color' => '#000000',
		],
	];

	public function __construct($owner, $tagName = '', $attributes = [], $content = '')
	{
		if (empty($tagName)) {
			$tagName = $this->defaultMarkup['tagName'];
		}
		if (count($attributes) === 0) {
			$attributes = $this->defaultMarkup['attributes'];
			$attributes['data-id'] = $owner->getId();
		}
		parent::__construct($owner, $tagName, $attributes, $content);
	}

	public function render(): string
	{
		$html = '';
		foreach ($this->elements as $element) {
			$html .= $element->getTag();
		}
		return $this->getTag($html);
	}

}