<?php


namespace common\widgets\Reveal\Plugins;


use yii\helpers\Url;

class Feedback extends AbstractPlugin implements PluginInterface
{

    public $configName = 'feedbackConfig';
    public $storyID;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'action' => Url::to(['feedback/create', 'id' => $this->storyID])
            ],
        ];
    }

    public function pluginCssFiles(): array
    {
        return [];
    }

    public function dependencies(): array
    {
        return [];
    }
}