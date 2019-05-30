<?php

namespace common\widgets\RevealButtons;

use yii\web\JsExpression;

class RightButton extends Button
{

	public function __construct()
	{
		$this->icon = 'icomoon-chevron-right';
		$this->className = 'custom-navigate-right';
		$this->title = 'Вперед';
		$this->action = new JsExpression('function() { 
		    if (Reveal.isLastSlide()) {
		        TransitionSlide.backToStory();
		    }
		    else {
		        Reveal.next();
		    }
		}');
	}

}