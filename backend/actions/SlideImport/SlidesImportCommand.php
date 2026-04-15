<?php

declare(strict_types=1);

namespace backend\actions\SlideImport;

class SlidesImportCommand
{
    /**
     * @var int
     */
    private $fromStoryId;
    /**
     * @var int
     */
    private $toStoryId;
    /**
     * @var array
     */
    private $slides;
    /**
     * @var bool
     */
    private $deleteSlidesAfterImport;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var int|null
     */
    private $insertAfterSlideId;

    public function __construct(
        int $fromStoryId,
        int $toStoryId,
        int $userId,
        array $slides,
        int $insertAfterSlideId = null,
        bool $deleteSlidesAfterImport = false
    ) {
        $this->fromStoryId = $fromStoryId;
        $this->toStoryId = $toStoryId;
        $this->slides = $slides;
        $this->deleteSlidesAfterImport = $deleteSlidesAfterImport;
        $this->userId = $userId;
        $this->insertAfterSlideId = $insertAfterSlideId;
    }

    public function getFromStoryId(): int
    {
        return $this->fromStoryId;
    }

    public function getToStoryId(): int
    {
        return $this->toStoryId;
    }

    public function getSlides(): array
    {
        return $this->slides;
    }

    public function isDeleteSlidesAfterImport(): bool
    {
        return $this->deleteSlidesAfterImport;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getInsertAfterSlideId(): ?int
    {
        return $this->insertAfterSlideId;
    }
}
