<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;

class AIStoryPlugin extends AbstractPlugin implements PluginInterface
{
    public $configName = 'aiStoryConfig';

    public function pluginConfig(): array
    {
        return [
            $this->configName => [],
        ];
    }

    public function pluginCssFiles(): array
    {
        return [];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/ai-story.js'),
        ];
    }
}
