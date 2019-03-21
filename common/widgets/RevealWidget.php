<?php

namespace common\widgets;

use yii\base\Widget;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use frontend\assets\RevealAsset;

class RevealWidget extends Widget
{

	public $data;
	public $story_id;
    public $options = [];

	public function run()
	{
	    echo Html::tag('div', $this->data, ['class'=>'reveal']);
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
        ];

        $options = $this->options;
        if (isset($options['width']) && $options['width'] > 0) {
            $config['width'] = $options['width'];
        }
        if (isset($options['height']) && $options['height'] > 0) {
            $config['height'] = $options['height'];
        }

        $config = array_merge($config,
                              $this->getStatisticsConfig(),
                              $this->getFeedbackConfig());

        $configJson = Json::htmlEncode($config);
        $js = "window.StoryRevealConfig = $configJson;";

        $view = $this->getView();
        $view->registerJs($js, View::POS_HEAD);

        RevealAsset::register($view);

        $view->registerJsFile('/js/story-reveal.js', ['depends' => ['yii\web\JqueryAsset']]);
        $view->registerCssFile('/css/wikids-reveal.css', ['depends' => 'frontend\assets\RevealAsset']);
    }

    protected function getStatisticsConfig()
    {
    	return [
            'statisticsConfig' => [
    	        'action' => Url::to(['statistics/write', 'id' => $this->story_id])
            ],
    	];
    }

    protected function getFeedbackConfig()
    {
        return [
            'feedbackConfig' => [
                'action' => Url::to(['feedback/create', 'id' => $this->story_id])
            ],
        ];
    }

}