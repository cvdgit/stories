<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;
use yii\helpers\Url;

class Statistics extends AbstractPlugin implements PluginInterface
{

    public $configName = 'statisticsConfig';
    public $storyID;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'action' => Url::to(['statistics/write', 'id' => $this->storyID])
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
            new Dependency('/js/player/plugins/story-reveal-statistics.js'),
        ];
    }
}