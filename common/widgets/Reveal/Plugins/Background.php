<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;

class Background extends AbstractPlugin implements PluginInterface
{

    public function pluginConfig()
    {
        return [];
    }

    public function pluginCssFiles()
    {
        return [];
    }

    public function dependencies()
    {
        return [
            new Dependency('/js/player/plugins/background.js'),
        ];
    }
}