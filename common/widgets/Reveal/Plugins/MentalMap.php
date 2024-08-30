<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;

class MentalMap extends AbstractPlugin implements PluginInterface
{
    public $configName = 'mentalMapConfig';
    public $storyId;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'story_id' => $this->storyId,
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
            new Dependency('/js/player/plugins/mental-map.js'),
        ];
    }
}
