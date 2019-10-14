<?php


namespace frontend\models;


use common\models\BaseAudioTrackModel;
use common\models\StoryAudioTrack;

class StoryTrackModel extends BaseAudioTrackModel
{

    public static function createTrack(string $name, int $storyID, int $userID, int $type, int $default)
    {
        $model = StoryAudioTrack::create($name, $storyID, $userID, $type, $default);
        $model->save();
        return $model;
    }

}