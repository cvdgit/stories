<?php

namespace common\widgets\Reveal\Plugins;

use common\helpers\Url;
use common\widgets\Reveal\Dependency;

class SlideLinksView extends AbstractPlugin implements PluginInterface
{

    public $configName = 'linksViewConfig';

    public function pluginConfig()
    {
        return [
            $this->configName => [
                'site' => Url::getServerUrl() . '/story/',
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
            new Dependency('/js/player/plugins/slide-links.js'),
        ];
    }
}
