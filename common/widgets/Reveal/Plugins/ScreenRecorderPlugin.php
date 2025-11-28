<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;
use Yii;

class ScreenRecorderPlugin extends AbstractPlugin implements PluginInterface
{
    /**
     * @var string
     */
    public $configName = 'screenRecorderConfig';
    /**
     * @var int
     */
    public $storyId;
    /**
     * @var int
     */
    public $userId;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'story_id' => $this->storyId,
                'user_id' => $this->userId,
                'ws_host' => Yii::$app->params['screen-recording.ws-host'],
            ],
        ];
    }

    public function pluginCssFiles(): array
    {
        $dep = new Dependency('/js/player/plugins/screen-recording.css');
        return [
            $dep->src,
        ];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/screen-recorder.js'),
        ];
    }
}
