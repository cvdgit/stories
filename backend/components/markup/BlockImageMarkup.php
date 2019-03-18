<?php

namespace backend\components\markup;

class BlockImageMarkup extends BlockMarkup
{

	protected $defaultMarkup = [
		'tagName' => 'div',
		'attributes' => [
			'data-block-id' => '',
			'class' => 'sl-block',
			'data-block-type' => 'image',
			'style' => 'min-width: 4px; min-height: 4px; width: 973px; height: 720px; left: 0px; top: 0px;',
		],
	];

}
