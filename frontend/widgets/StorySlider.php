<?php

namespace frontend\widgets;

use yii\base\Widget;
use common\models\Story;

class StorySlider extends Widget
{
	
	public function init()
	{
		parent::init();
	}

	public function run()
	{
		return $this->render('slider', [
			'models' => Story::forSlider(),
		]);
	}
}
