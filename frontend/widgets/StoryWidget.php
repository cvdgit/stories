<?php

namespace frontend\widgets;

use yii\base\Widget;
use common\models\Story;

class StoryWidget extends Widget
{

	public function init()
	{
		parent::init();
	}

	public function run()
	{
		return $this->render('_last_stories', [
			'models' => Story::findLastPublishedStories(),
		]);
	}

}