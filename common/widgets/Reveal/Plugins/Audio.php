<?php

namespace common\widgets\Reveal\Plugins;

use common\models\Story;
use common\models\StorySlide;
use common\services\StoryAudioService;
use common\widgets\Reveal\Dependency;
use Yii;

class Audio extends AbstractPlugin implements PluginInterface
{

    public $configName = 'audio';
    public $storyID;

    public $defaultAudios = true;
    public $prefix;
    public $autoplay = false;

    private $audioService;

    public function __construct(StoryAudioService $audioService, $config = [])
    {
        $this->audioService = $audioService;
        parent::__construct($config);
    }

    private function getAudioFiles(): array
    {
        $storyModel = Story::findOne($this->storyID);
        $slides = StorySlide::find()
            ->where(['story_id' => $this->storyID])
            ->indexBy('id')
            ->all();
        $track = $this->audioService->getStoryTrack($storyModel, null, Yii::$app->user->id);
        $files = [];
        if ($track !== null) {
            $path = $this->audioService->getAudioFilePath($this->storyID, $track->id);
            if (file_exists($path)) {
                $dir = opendir($path);
                while (false !== ($filename = readdir($dir))) {
                    if (!in_array($filename, array('.', '..'))) {
                        $slideId = explode('.', $filename)[0];
                        if (isset($slides[$slideId])) {
                            $files[] = [
                                'slide_id' => (int)$slideId,
                                'name' => $filename,
                            ];
                        }
                    }
                }
            }
        }
        return $files;
    }

    public function pluginConfig()
    {
        return [
            $this->configName => [
                'files' => $this->getAudioFiles(),
                'prefix' => $this->prefix, 	// audio files are stored in the "audio" folder
		        'suffix' => '.mp3',		// audio files have the ".ogg" ending
		        'textToSpeechURL' => null,  // the URL to the text to speech converter
		        'defaultNotes' => false, 	// use slide notes as default for the text to speech converter
		        'defaultText' => false, 	// use slide text as default for the text to speech converter
		        'advance' => 0, 		// advance to next slide after given time in milliseconds after audio has played, use negative value to not advance
		        'autoplay' => $this->autoplay,	// automatically start slideshow
		        'defaultDuration' => 5,	// default duration in seconds if no audio is available
		        'defaultAudios' => $this->defaultAudios,	// try to play audios with names such as audio/1.2.ogg
		        'playerOpacity' => 1,	// opacity value of audio player if unfocused
		        'playerStyle' => 'position: absolute; bottom: 4px; left: 25%; width: 50%; height:75px; z-index: 33;', // style used for container of audio controls
		        'startAtFragment' => false, // when moving to a slide, start at the current fragment or at the start of the slide
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
            new Dependency('/js/player/plugins/audio-slideshow.js'),
        ];
    }
}