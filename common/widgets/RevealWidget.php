<?php

namespace common\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use frontend\assets\RevealAsset;
use yii\helpers\Json;
use yii\helpers\Url;

class RevealWidget extends Widget
{

	public $data;
	public $story_id;

	public function run()
	{
	    echo Html::tag('div', $this->data, ['class'=>'reveal']);
	    $this->registerClientScript();
	}

    public function registerClientScript()
    {
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