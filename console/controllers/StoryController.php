<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\Story;
use common\components\StoryCover;
use Yii;
use yii\db\Query;

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

	public function actionClearStoryFiles()
    {

        $path = Yii::getAlias('@public/slides_file/');
        $existFiles = [];
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry !== '.' && $entry !== '..') {
                    $existFiles[] = $entry;
                }
            }
            closedir($handle);
        }

        $query = (new Query())
            ->from('{{%story}}')
            ->where('story_file IS NOT NULL')
            ->andWhere('source_id = 2');
        $storyFiles = [];
        foreach ($query->each() as $story) {
            $storyFiles[] = $story['story_file'];
        }

        $files = array_diff($existFiles, $storyFiles);
        foreach ($files as $fileName) {
            $this->stdout($fileName . PHP_EOL);
        }
    }

}
