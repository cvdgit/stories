<?php


namespace common\widgets\Reveal\Plugins;


interface PluginInterface
{

    public function pluginConfig();
    public function pluginCssFiles();
    public function dependencies();

}