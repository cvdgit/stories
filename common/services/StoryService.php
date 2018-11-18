<?php

namespace common\services;

use yii;

class StoryService
{

	protected $dropboxSerivce;

	public function __construct()
	{
		$this->dropboxSerivce = new StoryDropboxService();
	}

	public function getDropboxSerivce()
	{
		return $this->dropboxSerivce;
	}

    public function getCoverPath($cover, $web = false)
    {
        return ($web ? '' : Yii::getAlias('@public')) . '/slides_cover/' . $cover;
    }

    public function getImagesFolderPath($dropbox_story_filename, $web = false)
    {
        return ($web ? '' : Yii::getAlias('@public')) . '/slides/' . $dropbox_story_filename;
    }

    public function getStoryImages($dropbox_story_filename)
    {
        $dir  = opendir($this->getImagesFolderPath($dropbox_story_filename));
        $images = [];
        while (false !== ($filename = readdir($dir))) {
            if (!in_array($filename, array('.', '..'))) {
                $images[] = $this->getImagesFolderPath($dropbox_story_filename, true) . '/' . $filename;
            }
        }
        return $images;
    }

}