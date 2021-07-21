<?php

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;

class SlideState extends AbstractPlugin implements PluginInterface
{

    public $configName = 'slideState';
    public $storyID;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'story_id' => $this->storyID,
            ],
        ];
    }

    public function pluginCssFiles(): array
    {
        return [];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/slide-state.js'),
        ];
    }
}
