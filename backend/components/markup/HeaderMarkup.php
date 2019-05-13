<?php

namespace backend\components\markup;


class HeaderMarkup extends ParagraphMarkup
{

	protected $defaultMarkup = [
		'tagName' => 'h1',
		'attributes' => [
			'style' => 'font-size: 3em;',
		],
	];

}