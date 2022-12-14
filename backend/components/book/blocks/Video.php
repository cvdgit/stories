<?php

declare(strict_types=1);

namespace backend\components\book\blocks;

use common\models\SlideVideo;

class Video implements GuestBlockInterface
{
    /** @var string */
    private $videoId;
    /** @var string|null  */
    private $name;

    public function __construct(string $videoId)
    {
        $this->videoId = $videoId;
        $this->name = $this->getVideoName();
    }

    private function getVideoName(): ?string
    {
        $video = SlideVideo::findModelByVideoID($this->videoId);
        if ($video !== null) {
            return $video->title;
        }
        return '';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function isEmpty(): bool
    {
        return empty($this->videoId);
    }
}
