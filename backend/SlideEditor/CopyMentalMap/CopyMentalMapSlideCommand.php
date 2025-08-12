<?php

declare(strict_types=1);

namespace backend\SlideEditor\CopyMentalMap;

use Ramsey\Uuid\UuidInterface;

class CopyMentalMapSlideCommand
{
    /**
     * @var int
     */
    private $storyId;
    /**
     * @var int
     */
    private $currentSlideId;
    /**
     * @var UuidInterface
     */
    private $mentalMapCopyId;
    /**
     * @var string
     */
    private $mentalMapCopyName;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var bool
     */
    private $mentalMapRequired;

    public function __construct(
        int $storyId,
        int $currentSlideId,
        UuidInterface $mentalMapCopyId,
        string $mentalMapCopyName,
        int $userId,
        bool $mentalMapRequired
    ) {
        $this->storyId = $storyId;
        $this->currentSlideId = $currentSlideId;
        $this->mentalMapCopyId = $mentalMapCopyId;
        $this->mentalMapCopyName = $mentalMapCopyName;
        $this->userId = $userId;
        $this->mentalMapRequired = $mentalMapRequired;
    }

    public function getStoryId(): int
    {
        return $this->storyId;
    }

    public function getCurrentSlideId(): int
    {
        return $this->currentSlideId;
    }

    public function getMentalMapCopyId(): UuidInterface
    {
        return $this->mentalMapCopyId;
    }

    public function getMentalMapCopyName(): string
    {
        return $this->mentalMapCopyName;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function isMentalMapRequired(): bool
    {
        return $this->mentalMapRequired;
    }
}
