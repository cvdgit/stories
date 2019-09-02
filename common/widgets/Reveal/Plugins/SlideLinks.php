<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;

class SlideLinks extends AbstractPlugin implements PluginInterface
{

    public $configName = 'linksConfig';

    public $storyID;
    public $links;

    public function pluginConfig()
    {
        return [
            $this->configName => [
                'story_id' => $this->storyID,
                'links' => $this->links,
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
            new Dependency('/js/player/plugins/links.js'),
        ];
    }
}