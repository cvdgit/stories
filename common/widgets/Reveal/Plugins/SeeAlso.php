<?php

namespace common\widgets\Reveal\Plugins;

use common\helpers\Url;
use common\widgets\Reveal\Dependency;

class SeeAlso extends AbstractPlugin implements PluginInterface
{

    /** @var string */
    public $configName = 'seeAlso';

    /** @var int */
    public $storyID;

    public function pluginConfig()
    {
        return [
            $this->configName => [
                'action' => Url::to(['player/see-also-stories', 'story_id' => $this->storyID]),
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
            new Dependency('/js/player/plugins/see-also.js'),
        ];
    }
}