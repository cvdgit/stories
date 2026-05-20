<?php

declare(strict_types=1);

namespace backend\SlideEditor\SlideSettings;

class SaveSlideSettingsCommand
{
    /**
     * @var int
     */
    private $slideId;
    /**
     * @var SlideSettingsPayload
     */
    private $settings;

    public function __construct(int $slideId, SlideSettingsPayload $settings)
    {
        $this->slideId = $slideId;
        $this->settings = $settings;
    }

    public function getSlideId(): int
    {
        return $this->slideId;
    }

    public function getSettings(): SlideSettingsPayload
    {
        return $this->settings;
    }
}
