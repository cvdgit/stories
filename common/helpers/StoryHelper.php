<?php


namespace common\helpers;


use common\models\Story;
use common\models\StoryAudioTrack;
use Yii;
use yii\helpers\ArrayHelper;

class StoryHelper
{

    public static function getStoryArray(): array
    {
        return ArrayHelper::map(Story::find()->published()->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
    }

    public static function getStoryAudioTrackArray(Story $model)
    {
        $tracks = array_filter($model->storyAudioTracks, function(StoryAudioTrack $track) {
            return $track->isOriginal() || $track->isUserTrack(Yii::$app->user->id);
        });
        return ArrayHelper::map($tracks, 'id', 'name');
    }

}