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
        return  '/slides_cover/' . $this->story->cover;
    }

}