<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;

class Retelling extends AbstractPlugin implements PluginInterface
{
    public $configName = 'retelling';
    public $storyId;
    public $completed = [];

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'story_id' => $this->storyId,
                'completed' => $this->completed,
            ],
        ];
    }

    public function pluginCssFiles(): array
    {
        $dep = new Dependency('/js/player/plugins/retelling.css');
        return [
            $dep->src,
        ];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/retelling.js'),
        ];
    }
}
