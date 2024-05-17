<?php

declare(strict_types=1);

namespace common\widgets\RevealButtons;

use yii\web\JsExpression;

class RetellingAnswersButton extends Button
{
	public function __construct()
	{
		$this->icon = 'glyphicon glyphicon-tasks';
		$this->className = 'custom-feedback';
		$this->title = 'Пересказ с вопросами';
		$this->action = new JsExpression('function() { RetellingPlugin.beginAnswers(); }');
	}
}
