<?php

namespace common\services;

use yii;

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

    public function getCoverPath($cover, $web = false)
    {
        return ($web ? '' : Yii::getAlias('@public')) . '/slides_cover/' . $cover;
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

}