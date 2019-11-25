<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;

class Video extends AbstractPlugin implements PluginInterface
{

    public $configName = 'video';
    public $storyID;
    public $showControls = false;

    public function pluginConfig()
    {
        return [
            $this->configName => [
                'showControls' => $this->showControls,
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
            new Dependency('/js/player/plugins/video.js'),
        ];
    }
}