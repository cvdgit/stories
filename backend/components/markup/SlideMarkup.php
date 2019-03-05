<?php

namespace backend\components\markup;

use backend\components\StoryMarkup;

class SlideMarkup extends StoryMarkup
{

	public function __construct($owner)
	{
		parent::__construct('section', [
			'data-id' => $owner->getId(),
			'data-background-color' => '#000000',
		]);
	}

}