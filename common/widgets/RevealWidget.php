<?php

namespace common\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use frontend\assets\RevealAsset;

class RevealWidget extends Widget
{

	public $data;

	public function run()
	{
	    echo Html::tag('div', $this->data, ['class'=>'reveal']);
	    $this->registerClientScript();
	}

    public function registerClientScript()
    {
        $view = $this->getView();
        $asset = RevealAsset::register($view);
		$view->registerCssFile('/css/offline-v2.css');
        $js = <<< JS
Reveal.initialize({
	width: 1024,
	height: 576,
	transition: "none",
	backgroundTransition: "slide",
	center: true,
	controls: true,
	controlsLayout: 'bottom-right',
	controlsBackArrows: 'faded',
	progress: false,
	history: false,
	mouseWheel: false,
	showNotes: true,
	slideNumber: false,
	autoSlide: false,
	autoSlideStoppable: true,
	shuffle: false,
	loop: false,
	rtl: false
    //dependencies: [
    //    { src: 'js/reveal-plugins/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
    //    { src: 'js/reveal-plugins/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
    //    { src: 'js/reveal-plugins/highlight/highlight.js', async: true, callback: function() { hljs.initHighlighting(); hljs.initHighlightingOnLoad(); } },
    //    { src: 'js/reveal-plugins/notes/notes.js', async: true, condition: function() { return !!document.body.classList; } },
    //    { src: 'js/reveal-plugins/zoom/zoom.js', async: true }
    //]
});
JS;
		$view->registerJs($js);
		
		$js = <<< JS
		// Fullscreen
		// Reveal.addEventListener( 'ready', function( event ) {
		// 	let btnFullscreen = '<div class="cst-fullscreen" style="z-index: 11; cursor: pointer; bottom: 30px; right: 70px; position: absolute;"><i class="fas fa-arrows-alt"></i></div>';
		// 	$('.slides').after(btnFullscreen);
		// });
		// $( document ).ready(function() {
    	// 	$(".cst-fullscreen").on("click", function() {
		// 		Reveal.triggerKey(70);
		// 	});
		// });
JS;
		$view->registerJs($js);

    }

}