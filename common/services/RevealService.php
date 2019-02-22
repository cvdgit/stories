<?php

namespace common\services;

class RevealService
{

	public function wrapSlides($slides)
	{
		return '<div class="slides">' . $slides . '</div>';
	}

}