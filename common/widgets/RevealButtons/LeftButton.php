<?php

namespace common\widgets\RevealButtons;

use yii\web\JsExpression;

class LeftButton extends Button
{

	public function __construct()
	{
		$this->icon = 'icomoon-chevron-left';
		$this->className = 'custom-navigate-left';
		$this->title = 'Назад';
		$this->action = new JsExpression('function() { 
            if (Reveal.isFirstSlide()) {
                TransitionSlide.backToStory();
            }
            else {
                Reveal.prev();
            }
		}');
	}

}