<?php

namespace common\widgets\RevealButtons;

class LeftButton extends Button
{

	public function __construct()
	{
		$this->icon = 'fas fa-chevron-left';
		$this->className = 'custom-navigate-left';
		$this->title = 'Назад';
		$this->action = new \yii\web\JsExpression('function() { Reveal.prev(); }');
	}

}