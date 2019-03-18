<?php

namespace backend\components\markup;

class BlockImageContentMarkup extends BlockContentMarkup
{

	protected $defaultMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'class' => 'sl-block-content',
			'style' => 'z-index: 11;',
		],
	];

}
