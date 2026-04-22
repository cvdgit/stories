<?php

declare(strict_types=1);

namespace backend\MentalMap;

use JsonSerializable;

class MentalMapPayloadImage implements JsonSerializable
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var int
     */
    private $width;
    /**
     * @var int
     */
    private $height;
    /**
     * @var array
     */
    private $images;

    public function __construct(string $url, int $width, int $height, array $images = [])
    {
        $this->url = $url;
        $this->width = $width;
        $this->height = $height;
        $this->images = $images;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
            'images' => $this->images,
        ];
    }
}
