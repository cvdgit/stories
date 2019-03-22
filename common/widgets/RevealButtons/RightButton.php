<?php

namespace common\widgets\RevealButtons;

class RightButton extends Button
{

	public function __construct()
	{
		$this->icon = 'fas fa-chevron-right';
		$this->className = 'custom-navigate-right';
		$this->title = 'Вперед';
		$this->action = new \yii\web\JsExpression('function() { Reveal.next(); }');
	}

}