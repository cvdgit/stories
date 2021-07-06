<?php

namespace backend\components\image;

class SlideImage
{

    private const SLIDE_WIDTH = 1280;
    private const SLIDE_HEIGHT = 720;
    private const RATIO = 1.5;

    private $imageWidth;
    private $imageHeight;

    private $maxImageWidth;
    private $maxImageHeight;

    public function __construct(string $imagePath)
    {
        if (!file_exists($imagePath)) {
            throw new \DomainException('Story slide image not exists');
        }
        [$this->imageWidth, $this->imageHeight] = getimagesize($imagePath);
        $this->maxImageWidth = self::SLIDE_WIDTH * self::RATIO;
        $this->maxImageHeight = self::SLIDE_HEIGHT * self::RATIO;
    }

    public function needResize(): bool
    {
        return $this->imageWidth > $this->maxImageWidth || $this->imageHeight > $this->maxImageHeight;
    }

    public function getNaturalSize(): ImageSize
    {
        return new ImageSize($this->imageWidth, $this->imageHeight);
    }

    public function getEditorImageSize(): ImageSize
    {
        $ratio = $this->imageWidth / $this->imageHeight;
        if (self::SLIDE_WIDTH / self::SLIDE_HEIGHT > $ratio) {
            $imageWidth = self::SLIDE_HEIGHT * $ratio;
            $imageHeight = self::SLIDE_HEIGHT;
        } else {
            $imageHeight = self::SLIDE_WIDTH / $ratio;
            $imageWidth = self::SLIDE_WIDTH;
        }
        return new ImageSize($imageWidth, $imageHeight);
    }

    public function getResizeImageSize(): ImageSize
    {
        if (!$this->needResize()) {
            return $this->getNaturalSize();
        }
        return new ImageSize($this->maxImageWidth, $this->maxImageHeight);
    }
}
