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
        RevealAsset::register($view);
    }

}