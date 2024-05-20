<?php

declare(strict_types=1);

namespace common\widgets\RevealButtons;

use yii\web\JsExpression;

class RetellingButton extends Button
{
	public function __construct()
	{
		$this->icon = 'glyphicon glyphicon-book';
		$this->className = 'control-retelling';
		$this->title = 'Пересказ';
		$this->action = new JsExpression('function() { RetellingPlugin.begin(); }');
	}
}
