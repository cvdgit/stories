<?php

declare(strict_types=1);

namespace backend\SlideEditor\SlideSettings;

use JsonSerializable;

class SlideSettingsPayload implements JsonSerializable
{
    /**
     * @var bool
     */
    private $speakSlideText;

    public function __construct(bool $speakSlideText)
    {
        $this->speakSlideText = $speakSlideText;
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['speakSlideText'] ?? false
        );
    }

    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    public function asArray(): array
    {
        return [
            'speakSlideText' => $this->speakSlideText,
        ];
    }

    public function isSpeakSlideText(): bool
    {
        return $this->speakSlideText;
    }
}
