<?php

namespace common\services;

use backend\components\story\reader\PowerPointReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\SourcePowerPointForm;
use common\models\Story;
use DomainException;
use matperez\yii2unisender\UniSender;
use yii;
use yii\helpers\Url;
use yii\web\View;

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
        $reader = new PowerPointReader($fileName, Yii::getAlias('@public') . $imagesFolder, $imagesFolder);
        $story = $reader->load();
        $writer = new HTMLWriter();
        $body = $writer->renderStory($story);
        $form->saveSource($body, $story->getSlideCount());
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
        if (empty($model->cover)) {
            throw new DomainException('Не установлена обложка');
        }
        if (empty($model->story_file)) {
            throw new DomainException('Не найден файл PowerPoint');
        }
        if ((int)$model->slides_number === 0) {
            throw new DomainException('В истории отсутствуют слайды');
        }
        $model->status = Story::STATUS_PUBLISHED;

        $view = Yii::createObject(View::class);

        /** @var UniSender $unisender */
        $unisender = Yii::$app->unisender;
        $api = $unisender->getApi();
        $result = $api->createEmailMessage([
            'sender_name' => 'Wikids',
            'sender_email' => 'info@wikids.ru',
            'subject' => 'Новая история на Wikids',
            'body' => $view->render('@common/mail/newStory-html', ['story' => $model]),
            'list_id' => 17823821,
        ]);
        $messageID = $result['result']['message_id'];

        $result = $api->createCampaign([
            'message_id' => $messageID,
        ]);

        $model->save(false, ['status']);
    }

    public function unPublishStory(Story $model): void
    {
        $model->status = Story::STATUS_DRAFT;
        $model->save(false, ['status']);
    }

}