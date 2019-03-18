<?php

namespace backend\components\markup;

class BlockHeaderMarkup extends BlockMarkup
{

	protected $defaultMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'data-block-id' => '',
			'class' => 'sl-block',
			'data-block-type' => 'text',
			'style' => 'width: 1200px; left: 14px; top: 294px; height: auto;',
		],
	];

}
