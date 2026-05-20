<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;

class SpeakSlideText extends AbstractPlugin implements PluginInterface
{
    public const SPEAK_SLIDE_TEXT_THRESHOLD = 50;

    public $storyId;
    public $userId;
    public $speakTextSlides;
    public $configName = 'speakSlideTextConfig';

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'storyId' => $this->storyId,
                'userId' => $this->userId,
                'speakTextSlides' => $this->speakTextSlides,
                'threshold' => self::SPEAK_SLIDE_TEXT_THRESHOLD,
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
            new Dependency('/js/player/plugins/speak-slide-text.js'),
        ];
    }
}
