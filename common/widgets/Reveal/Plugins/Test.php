<?php

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;
use yii\helpers\Url;

class Test extends AbstractPlugin implements PluginInterface
{
    public $configName = 'testConfig';
    public $storyID;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'action' => Url::to(['story/get-story-test']),
                'storeAction' => Url::to(['story/store-test-result', 'story_id' => $this->storyID]),
                'storyBodyAction' => Url::to(['story/get-story-body']),
                'story_id' => $this->storyID,
                'initAction' => Url::to(['question/init']),
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
            new Dependency('/js/player/plugins/story-test.js'),
        ];
    }
}