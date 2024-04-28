<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use common\models\Story;
use common\services\StoryAudioService;
use common\widgets\Reveal\Dependency;
use Yii;
use yii\helpers\Url;

class Retelling extends AbstractPlugin implements PluginInterface
{
    public $configName = 'retelling';

    /** @var Story */
    public $story;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [],
        ];
    }

    public function pluginCssFiles(): array
    {
        return [];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/retelling.js'),
        ];
    }
}
