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
            //"minScale" => 1.0,
            //"maxScale" => 0.6,
            //"margin" => 0,
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

        $configJson = Json::htmlEncode($config);
        $js = "window.StoryRevealConfig = $configJson;";
        $this->getView()->registerJs($js, View::POS_HEAD);

        $view = $this->getView();
        RevealAsset::register($view);

        $this->registerStatistics();
    }

    public function registerStatistics()
    {
    	$config = [
    		'action' => Url::to(['statistics/write', 'id' => $this->story_id]),
    		'modelName' => 'StoryStatistics',
    	];
    	$configJson = Json::htmlEncode($config);
        $js = "Reveal.configure({statisticsConfig: $configJson});";
        $this->getView()->registerJs($js);
    }

}