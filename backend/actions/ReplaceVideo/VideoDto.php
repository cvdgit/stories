<?php

declare(strict_types=1);

namespace backend\actions\ReplaceVideo;

class VideoDto
{
    private $id;
    private $source;
    private $name;
    private $videoId;

    public function __construct(int $id, int $source, string $name, string $videoId)
    {
        $this->id = $id;
        $this->source = $source;
        $this->name = $name;
        $this->videoId = $videoId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSource(): int
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVideoId(): string
    {
        return $this->videoId;
    }
}
