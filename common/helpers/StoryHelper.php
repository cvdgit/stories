<?php


namespace common\helpers;


use common\models\Story;
use yii\helpers\ArrayHelper;

class StoryHelper
{

    public static function getStoryArray(): array
    {
        return ArrayHelper::map(Story::find()->published()->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
    }

    public static function getStoryAudioTrackArray(Story $model)
    {
        return ArrayHelper::map($model->storyAudioTracks, 'id', 'name');
    }

}