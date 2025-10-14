<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;

class ContentMentalMap extends AbstractPlugin implements PluginInterface
{
    public $configName = 'contentMentalMapConfig';
    public $storyId;
    public $mentalMaps = [];

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'story_id' => $this->storyId,
                'mentalMaps' => $this->mentalMaps,
            ],
        ];
    }

    public function pluginCssFiles(): array
    {
        return [
            (new Dependency('/js/player/plugins/retelling.css'))->src,
        ];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/content-mental-map.js'),
        ];
    }
}
