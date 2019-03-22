<?php

namespace common\widgets\RevealButtons;

class FullscreenButton extends Button
{

	public function __construct()
	{
		$this->icon = 'fas fa-arrows-alt';
		$this->className = 'custom-fullscreen';
		$this->title = 'Полноэкранный режим';
		$js = <<< JS
function() {
	WikidsPlayer.toggleFullscreen();
	var el = $(this).find('i');
	el.removeClass('fa-arrows-alt').removeClass('fa-expand-arrows-alt');
	WikidsPlayer.inFullscreen() ? el.addClass('fa-arrows-alt') : el.addClass('fa-expand-arrows-alt');
}
JS;
		$this->action = new \yii\web\JsExpression($js);
	}

}