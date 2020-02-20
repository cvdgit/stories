<?php

namespace common\services;

use backend\components\queue\GenerateBookStoryJob;
use backend\components\queue\PublishStoryJob;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\reader\PowerPointReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\SourcePowerPointForm;
use common\models\Story;
use common\models\StorySlide;
use DomainException;
use frontend\models\SlideAudio;
use http\Exception\RuntimeException;
use yii;
use yii\helpers\Url;

class StoryService
{

	protected $dropboxSerivce;
    protected $powerPointService;

	public function __construct()
	{
		$this->dropboxSerivce = new StoryDropboxService();
        $this->powerPointService = new StoryPowerPointService();
	}

	public function getDropboxSerivce()
	{
		return $this->dropboxSerivce;
	}

    public function getPowerPointSerivce()
    {
        return $this->powerPointService;
    }

    public function importStoryFromPowerPoint(SourcePowerPointForm $form): void
    {
        $fileName = Yii::getAlias('@public') . '/slides_file/' . $form->storyFile;
        $imagesFolder = '/slides/' . $form->storyFile;
        $reader = new PowerPointReader($fileName, Yii::getAlias('@public'), $imagesFolder);
        $story = $reader->load();

        $writer = new HTMLWriter();
        $slides = $story->getSlides();
        $command = Yii::$app->db->createCommand();
        $command->delete('{{%story_slide}}', 'story_id = :story', [':story' => $form->storyId])->execute();
        foreach ($slides as $slide) {
            $data = $writer->renderSlide($slide);
            $command->insert('{{%story_slide}}', [
                'story_id' => $form->storyId,
                'data' => $data,
                'number' => $slide->getSlideNumber(),
                'created_at' => time(),
                'updated_at' => time(),
            ])->execute();
        }

        $storyModel = Story::findModel($form->storyId);
        $storyModel->slides_number = count($slides);
        $storyModel->save(false, ['slides_number']);

        Yii::$app->queue->push(new GenerateBookStoryJob([
            'storyID' => $form->storyId,
        ]));
    }

    public function getCoverPath($cover, $web = false)
    {
        return ($web ? Url::base(true) : Yii::getAlias('@public')) . '/slides_cover/' . $cover;
    }

    public function getImagesFolderPath($model, $web = false)
    {
        return ($web ? '' : Yii::getAlias('@public')) . '/slides/' . $model->story_file;
    }

    public function getStoryImages($model)
    {
        $dir  = opendir($this->getImagesFolderPath($model));
        $images = [];
        while (false !== ($filename = readdir($dir))) {
            if (!in_array($filename, array('.', '..'))) {
                $images[] = $this->getImagesFolderPath($model, true) . '/' . $filename;
            }
        }
        return $images;
    }

    public static function getStoryFilePath($storyFile)
    {
        return Yii::getAlias('@public') . '/slides_file/' . $storyFile;
    }

    protected function getStoryFiles($story)
    {
        $files = [];
        if (!empty($story->cover)) {
            $files[] = $this->getCoverPath($story->cover);
        }
        if (!empty($story->story_file)) {
            $files[] = self::getStoryFilePath($story->story_file);
            $imagesFolder = Yii::getAlias('@public') . '/slides/' . $story->story_file . '/';
            if (file_exists($imagesFolder)) {
                $dir = new \DirectoryIterator($imagesFolder);
                foreach ($dir as $file) {
                    if ($file->isFile()) {
                        $files[] = $file->getPathname();
                    }
                }
                $files[] = $imagesFolder;
            }
        }
        return $files;
    }

    private function _deleteFile($file)
    {
        if (file_exists($file)) {
            if (is_dir($file)) {
                rmdir($file);
            }
            else {
                unlink($file);
            }
        }
    }

    public function deleteStoryFiles($story)
    {
        $files = $this->getStoryFiles($story);
        foreach ($files as $fileName) {
            $this->_deleteFile($fileName);
        }
    }

    public function publishStory(Story $model): void
    {
        if ($model->isPublished()) {
            throw new DomainException('История уже опубликована');
        }
        if (empty($model->cover)) {
            throw new DomainException('Не установлена обложка');
        }
        if ((int)$model->slides_number === 0) {
            throw new DomainException('В истории отсутствуют слайды');
        }

        if ($model->submitPublicationTask()) {
            Yii::$app->queue->push(new PublishStoryJob([
                'storyID' => $model->id,
            ]));
        }

        $model->publishStory();
    }

    public function unPublishStory(Story $model): void
    {
        $model->status = Story::STATUS_DRAFT;
        $model->save(false, ['status']);
    }

    public function getDefaultStoryView()
    {
        $view = 'book';
        // !Yii::$app->devicedetect->isMobile()
        if (!Yii::$app->user->isGuest) {
            $view = 'slides';
        }
        return $view;
    }

}