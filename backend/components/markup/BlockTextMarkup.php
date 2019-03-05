<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class BlockTextMarkup extends StoryMarkup
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
	protected $defaultContentMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'class' => 'sl-block-content',
			'data-placeholder-tag' => 'p',
			'data-placeholder-text' => 'Text',
			'style' => 'z-index: 12; text-align: left;',
		],
	];

	protected $text;

	public function __construct($owner, $new = false)
	{
		if ($new) {
			parent::__construct($this->defaultMarkup['tagName'], $this->defaultMarkup['attributes']);
			$contentMarkup = new StoryMarkup($this->defaultContentMarkup['tagName'], $this->defaultContentMarkup['attributes']);
			$this->setContentMarkup($contentMarkup);
		}
	}

	public function setText($text)
	{
		$this->text = new StoryMarkup('p', [
			'style' => 'color: #FFFFFF; font-size: 0.8em;',
		], $text);
	}

	public function getText()
	{
		return $this->text;
	}

}