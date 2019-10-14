<?php

namespace frontend\widgets;

use common\models\Story;
use common\services\StoryAudioService;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class RecorderWidget extends Widget
{

    /** @var Story */
    public $story;

    protected $audioService;

    public function __construct(StoryAudioService $audioService, $config = [])
    {
        $this->audioService = $audioService;
        parent::__construct($config);
    }

    public function run()
    {
        return $this->render('_recorder', [
            'model' => $this->story,
            'currentTrack' => $this->getCurrentTrack(),
            'audioTrackArray' => $this->getStoryAudioTrackArray(),
        ]);
    }

    protected function getStoryAudioTrackArray(): array
    {
        $tracks = $this->story->getUserAudioTracks(Yii::$app->user->id);
        return ArrayHelper::map($tracks, 'id', 'name');
    }

    protected function getCurrentTrack()
    {
        $currentTrackID = Yii::$app->request->get('track_id');
        return $this->audioService->getStoryTrack($this->story, $currentTrackID, Yii::$app->user->id);
    }

}