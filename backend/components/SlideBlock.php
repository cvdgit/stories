<?php

namespace backend\components;

use Yii;

abstract class SlideBlock
{

	protected $id;

	protected $width;
	protected $height;

	protected $markup;

	public function __construct()
	{
		$this->id = Yii::$app->security->generateRandomString();
	}

	public function getId()
	{
		return $this->id;
	} 

	public function getWidth()
	{
		return $this->width;
	}

	public function setWidth($width)
	{
		$this->width = $width;
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function setHeight($height)
	{
		$this->height = $height;
	}

	public function setMarkup($markup)
	{
		$this->markup = $markup;
	}

	public function getMarkup()
	{
		return $this->markup;
	}

}
