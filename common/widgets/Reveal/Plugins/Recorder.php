<?php

namespace common\widgets\Reveal\Plugins;

use common\models\Story;
use common\services\StoryAudioService;
use common\widgets\Reveal\Dependency;
use Yii;
use yii\helpers\Url;

class Recorder extends AbstractPlugin implements PluginInterface
{

    /** @var string */
    public $configName = 'wikidsRecorder';

    /** @var Story */
    public $story;

    protected $audioService;

    public function __construct(StoryAudioService $audioService, $config = [])
    {
        $this->audioService = $audioService;
        parent::__construct($config);
    }

    public function pluginConfig()
    {
        return [
            $this->configName => [
                'tracks' => $this->getStoryAudioTrackArray(),
                'currentTrack' => $this->getCurrentTrack(),
                'createTrackAction' => Url::to(['player/create-audio-track', 'story_id' => $this->story->id]),
                'deleteTrackAction' => Url::to(['player/delete-track']),
            ],
        ];
    }

    public function pluginCssFiles()
    {
        return [];
    }

    public function dependencies()
    {
        return [
            new Dependency('/js/player/plugins/recorder.js'),
        ];
    }

    protected function getStoryAudioTrackArray(): array
    {
        return $this->story->getUserAudioTracks(Yii::$app->user->id);
    }

    protected function getCurrentTrack()
    {
        $currentTrackID = Yii::$app->request->get('track_id');
        return $this->audioService->getStoryTrack($this->story, $currentTrackID, Yii::$app->user->id);
    }

}