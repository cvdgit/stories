<?php

namespace common\widgets;

use yii\base\Widget;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use frontend\assets\RevealAsset;

class RevealWidget extends Widget
{

    public $id;
	public $data;
	public $storyId;
    public $initScript;
    public $options = [];

	public function run()
	{
        if (empty($this->data)) {
            $this->data = '<div class="slides"></div>';
        }
	    echo Html::tag('div', $this->data, ['class'=>'reveal', 'id' => $this->id]);
	    $this->registerClientScript();
	}

    public function registerClientScript()
    {
        $config = [
            "width" => 1280,
            "height" => 720,
            "transition" => "none",
            "backgroundTransition" => "slide",
            "center" => true,
            "controls" => false,
            "controlsLayout" => "bottom-right",
            "controlsBackArrows" => "faded",
            "progress" => true,
            "history" => true,
            "mouseWheel" => false,
            "showNotes" => true,
            "slideNumber" => true,
            "autoSlide" => false,
            "autoSlideStoppable" => true,
            "shuffle" => false,
            "loop" => false,
            "hash" => true,
            "hashOneBasedIndex" => true,
            "rtl" => false,
            "dependencies" => [
                // ["src" => "js/reveal-plugins/markdown/marked.js", "condition" => new JsExpression("function() { return !!document.querySelector( '[data-markdown]' ); }")],
                // ["src" => "js/reveal-plugins/markdown/markdown.js", "condition" => new JsExpression("function() { return !!document.querySelector( '[data-markdown]' ); }")],
                // ["src" => "js/reveal-plugins/highlight/highlight.js", "async" => true, "callback" => new JsExpression("function() { hljs.initHighlighting(); hljs.initHighlightingOnLoad(); }")],
                // ["src" => "js/reveal-plugins/notes/notes.js", "async" => true, "condition" => new JsExpression("function() { return !!document.body.classList; }")],
                // ["src" => "js/reveal-plugins/zoom/zoom.js", "async" => true],
            ],
        ];

        $options = $this->options;
        if (isset($options['width']) && $options['width'] > 0) {
            $config['width'] = $options['width'];
        }
        if (isset($options['height']) && $options['height'] > 0) {
            $config['height'] = $options['height'];
        }
        if (isset($options["dependencies"]) && is_array($options["dependencies"])) {
            $config["dependencies"] = array_merge($config["dependencies"], $options["dependencies"]);
        }

        $config = array_merge($config,
                              $this->getStatisticsConfig(),
                              $this->getFeedbackConfig());

        $configJson = Json::htmlEncode($config);
        $js = "window.StoryRevealConfig = $configJson;";

        $view = $this->getView();
        $view->registerJs($js, View::POS_HEAD);

        RevealAsset::register($view);

        if (!empty($this->initScript)){
            $view->registerJsFile($this->initScript, ['depends' => ['yii\web\JqueryAsset', 'frontend\assets\RevealAsset']]);
        }
        $view->registerCssFile('/css/wikids-reveal.css', ['depends' => ['frontend\assets\RevealAsset']]);
    }

    protected function getStatisticsConfig()
    {
    	return [
            'statisticsConfig' => [
    	        'action' => Url::to(['statistics/write', 'id' => $this->storyId])
            ],
    	];
    }

    protected function getFeedbackConfig()
    {
        return [
            'feedbackConfig' => [
                'action' => Url::to(['feedback/create', 'id' => $this->storyId])
            ],
        ];
    }

}