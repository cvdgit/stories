<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;

class Actions extends AbstractPlugin implements PluginInterface
{

    public $configName = 'actions';

    public function pluginConfig()
    {
        return [
            $this->configName => [
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
            new Dependency('/js/player/plugins/actions.js'),
        ];
    }
}