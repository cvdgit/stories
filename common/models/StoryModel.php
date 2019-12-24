<?php


namespace common\models;


use Yii;
use yii\helpers\Url;

class StoryModel
{

    protected $story;

    public function __construct(Story $story)
    {
        $this->story = $story;
    }

    public function getCoverPath(): string
    {
        return Yii::getAlias('@public') . $this->getCoverRelativePath();
    }

    public function getCoverRelativePath(): string
    {
        return  '/slides_cover/list/' . $this->story->cover;
    }

    public function getImagesFolderPath($relative = false)
    {
        return ($relative ? '' : Yii::getAlias('@public')) . '/slides/' . $this->story->story_file;
    }

}