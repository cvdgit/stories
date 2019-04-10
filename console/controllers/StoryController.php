<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Story;
use common\components\StoryCover;

class StoryController extends Controller
{

	public function actionMakeCovers()
	{
		$models = Story::find()->where('cover is not null')->all();
		$this->stdout('Всего историй - ' . count($models) . PHP_EOL);
		foreach ($models as $model) {
			$this->stdout($model->title . PHP_EOL);
			
			$coverPath = StoryCover::getSourceFilePath($model->cover, true);

			$path = StoryCover::createListThumbnail($coverPath);
			$this->stdout('[+] ' . $path . PHP_EOL);
			
			$path = StoryCover::createStoryThumbnail($coverPath);
			$this->stdout('[+] ' . $path . PHP_EOL);
			
			$this->stdout('' . PHP_EOL);
		}
		$this->stdout('Done!' . PHP_EOL);
	}

}
