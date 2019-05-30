<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;
use yii\helpers\Url;

class Transition extends AbstractPlugin implements PluginInterface
{
    public $configName = 'transitionConfig';
    public $storyID;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'action' => Url::to(['story/get-story-body']),
                'story_id' => $this->storyID,
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
            new Dependency('/js/player/plugins/story-transition.js'),
        ];
    }
}