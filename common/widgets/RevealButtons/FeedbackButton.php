<?php

namespace common\widgets\RevealButtons;

use yii\web\JsExpression;

class FeedbackButton extends Button
{

	public function __construct()
	{
		$this->icon = 'icomoon-comment-o';
		$this->className = 'custom-feedback';
		$this->title = 'Сообщить об опечатке на слайде';
		$this->action = new JsExpression('function() { WikidsStoryFeedback.sendFeedback(); }');
	}

}