<?php

namespace common\widgets\RevealButtons;

class FeedbackButton extends Button
{

	public function __construct()
	{
		$this->icon = 'far fa-comment';
		$this->className = 'custom-feedback';
		$this->title = 'Сообщить об опечатке на слайде';
		$this->action = new \yii\web\JsExpression('function() { WikidsStoryFeedback.sendFeedback(); }');
	}

}