<?php

namespace frontend\widgets;

use common\models\Story;
use common\services\StoryAudioService;
use yii\base\Widget;

class StoryAudio extends Widget
{

    public $storyID;

    protected $audioService;

    public function __construct(StoryAudioService $audioService, $config = [])
    {
        $this->audioService = $audioService;
        parent::__construct($config);
    }

    public function run()
    {
        $story = Story::findModel($this->storyID);
        $track = $this->audioService->getStoryTrack($story);

        if ($track === null) {
            return;
        }

        $path = $this->audioService->getTrackRelativePath($story->id, $track->id);
        $files = $this->audioService->trackFileList($story->id, $track->id);

        return $this->render('_audio', [
            'path' => $path . DIRECTORY_SEPARATOR,
            'files' => $files,
        ]);
    }

}