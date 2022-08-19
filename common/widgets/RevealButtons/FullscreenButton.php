<?php

namespace common\widgets\RevealButtons;

class FullscreenButton extends Button
{

	public function __construct()
	{
		$this->icon = 'icomoon-arrows';
		$this->className = 'custom-fullscreen';
		$this->title = 'Полноэкранный режим';
        /** @noinspection SyntaxError */
        $js = <<< JS
function() {
	WikidsPlayer.toggleFullscreen();
	var el = $(this).find('i');
	el.removeClass('icomoon-arrows').removeClass('icomoon-arrows-alt');
	WikidsPlayer.inFullscreen() ? el.addClass('icomoon-arrows') : el.addClass('icomoon-arrows-alt');
}
JS;
		$this->action = new \yii\web\JsExpression($js);
	}

}
