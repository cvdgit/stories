<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class BlockHeaderMarkup extends StoryMarkup
{

	protected $text;

	public function __construct($owner)
	{
		parent::__construct('div', [
			'data-block-id' => $owner->getId(),
			'class' => 'sl-block',
			'data-block-type' => 'text',
			'style' => 'width: 1200px; left: 14px; top: 294px; height: auto;',
		]);

		$contentMarkup = new StoryMarkup('div', [
			'class' => 'sl-block-content',
			'data-placeholder-tag' => 'h1',
			'data-placeholder-text' => 'Text',
			'style' => 'color: rgb(255, 255, 255); z-index: 10; text-align: center;',
		]);
		$this->setContentMarkup($contentMarkup);
	}

	public function setText($text)
	{
		$this->text = new StoryMarkup('h1', [
			'style' => 'font-size: 3.5em;',
		], $text);
	}

	public function getText()
	{
		return $this->text;
	}

}