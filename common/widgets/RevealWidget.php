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
	margin: 0.05,
	controls: true,
	progress: true,
	history: false,
	mouseWheel: false,
	showNotes: true,
	slideNumber: false,
	autoSlide: 0 || 0,
	autoSlideStoppable: true,
	center: false,
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
    }

}