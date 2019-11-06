<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;

class Audio extends AbstractPlugin implements PluginInterface
{

    public $configName = 'audio';
    public $storyID;

    public $defaultAudios = true;
    public $prefix;
    public $autoplay = false;

    public function pluginConfig()
    {
        return [
            $this->configName => [
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