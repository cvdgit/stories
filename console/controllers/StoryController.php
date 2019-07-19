<?php

namespace console\controllers;

use backend\components\story\reader\HTMLReader;
use backend\components\story\writer\HTMLWriter;
use common\models\StorySlide;
use http\Exception\RuntimeException;
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
                    $pathParts = pathinfo($path . $entry);
                    if ($pathParts['extension'] === 'pptx') {
                        $existFiles[] = $entry;
                    }
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

            $imagesPath = Yii::getAlias('@public/slides/' . $fileName . '/');
            if (file_exists($imagesPath)) {
                array_map('unlink', glob($imagesPath . '*.*'));
            }

            $filePath = $path . $fileName;
            unlink($filePath);
        }

        $this->stdout('Done!' . PHP_EOL);
    }

    public function actionChangeCategories(): void
    {
        $query = 'INSERT INTO {{%story_category}} (story_id, category_id) SELECT t.id, t.category_id FROM {{%story}} AS t WHERE t.category_id IS NOT NULL';
        $command = Yii::$app->db->createCommand($query);
        $command->execute();
        $this->stdout('Done!' . PHP_EOL);
    }

    public function actionCreateSlides()
    {
        $query = (new Query())->from('{{%story}}')->where('body IS NOT NULL');
        $command = Yii::$app->db->createCommand();
        $writer = new HTMLWriter();
        foreach ($query->each() as $row) {

            try {
                $reader = new HTMLReader($row['body']);
                $story = $reader->load();
            }
            catch (\Exception $e) {
                throw new RuntimeException('Error on story ' . $row['id']);
            }

            $slides = $story->getSlides();
            foreach ($slides as $slide) {
                $data = $writer->renderSlide($slide);
                $command->insert('{{%story_slide}}', [
                    'story_id' => $row['id'],
                    'data' => $data,
                    'number' => $slide->getSlideNumber(),
                    'created_at' => time(),
                    'updated_at' => time(),
                ])->execute();
            }
            $this->stdout(count($slides) . PHP_EOL);
        }
        $this->stdout('Done!' . PHP_EOL);
    }

    public function actionHideFirstSlide()
    {
        $query = (new Query())->from('{{%story}}')->where('body IS NOT NULL');
        foreach ($query->each() as $row) {
            $model = StorySlide::findFirstSlide($row['id']);
            $model->status = StorySlide::STATUS_HIDDEN;
            $save = $model->save(false, ['status']);
            $this->stdout($row['id'] . ' - ' . $save . PHP_EOL);
        }
        $this->stdout('Done!' . PHP_EOL);
    }

}
