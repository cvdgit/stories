<?php

namespace frontend\widgets;

use common\models\Story;
use common\services\StoryAudioService;
use frontend\models\StoryTrackModel;
use Yii;
use yii\base\Widget;

class StoryAudio extends Widget
{

    public $storyID;

    public function run()
    {
        $story = Story::findModel($this->storyID);
        $track = $story->getStoryTrack(Yii::$app->user->id);
        if ($track === null) {
            return;
        }
        $path = StoryTrackModel::getTrackRelativePath($story->id, $track->id);
        $files = StoryTrackModel::trackFileList($story->id, $track->id);
        return $this->render('_audio', [
            'path' => $path . DIRECTORY_SEPARATOR,
            'files' => $files,
        ]);
    }

}