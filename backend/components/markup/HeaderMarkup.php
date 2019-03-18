<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class HeaderMarkup extends ParagraphMarkup
{

	protected $defaultMarkup = [
		'tagName' => 'h1',
		'attributes' => [
			'style' => 'font-size: 3.5em;',
		],
	];

}