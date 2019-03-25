<?php

namespace common\services;

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

    public function importStoryFromPowerPoint($form)
    {
        $story = $this->powerPointService->loadStory($form);
        $slidesNumber = $story->getSlideCount();

        $storyEditor = new \backend\components\StoryEditor($story);
        $body = $storyEditor->getStoryMarkup();

        $form->saveSource($body, $slidesNumber);
    }

    public function importStoryFromDropbox()
    {

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

    public function userCanViewStory(\common\models\Story $story, $user = null): bool
    {
        return !$story->bySubscription() || ($story->bySubscription() && $user && $user->hasSubscription());
    }

}