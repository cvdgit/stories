<?php

namespace backend\components\markup;

class BlockHeaderContentMarkup extends BlockContentMarkup
{

	protected $defaultMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'class' => 'sl-block-content',
			'data-placeholder-tag' => 'h1',
			'data-placeholder-text' => 'Text',
			'style' => 'color: rgb(255, 255, 255); z-index: 10; text-align: center;',
		],
	];

}
