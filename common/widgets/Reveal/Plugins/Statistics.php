<?php

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;
use yii\helpers\Url;

class Statistics extends AbstractPlugin implements PluginInterface
{

    public $configName = 'statisticsConfig';
    public $storyID;
    public $studyTaskID;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'story_id' => $this->storyID,
                'action' => Url::to(['statistics/write']),
                'study_task_id' => $this->studyTaskID,
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