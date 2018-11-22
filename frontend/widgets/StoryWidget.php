<?php

namespace frontend\widgets;

use yii\base\Widget;
use common\models\Story;
use yii\data\ActiveDataProvider;

class StoryWidget extends Widget
{

	public function init()
	{
		parent::init();

	}

	public function run()
	{
        $dataProvider = new ActiveDataProvider([
            'query' => Story::findLastPublishedStories(),
        ]);
		return $this->render('_last_stories', [
			'models' => $dataProvider->getModels(),
		]);
	}

}