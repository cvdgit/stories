<?php

namespace common\services;

class RevealService
{

	public $defaultImageWidth = 1459;
	public $defaultImageHeight = 1080;

	public function wrapSlides($slides)
	{
		return '<div class="slides">' . $slides . '</div>';
	}

}