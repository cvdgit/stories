<?php

declare(strict_types=1);

namespace backend\SlideEditor\CopyRetelling;

use Ramsey\Uuid\UuidInterface;

class CopyRetellingSlideCommand
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
    private $retellingCopyId;
    /**
     * @var string
     */
    private $retellingName;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var bool
     */
    private $retellingRequired;

    public function __construct(
        int $storyId,
        int $currentSlideId,
        UuidInterface $retellingCopyId,
        string $retellingName,
        int $userId,
        bool $retellingRequired
    ) {
        $this->storyId = $storyId;
        $this->currentSlideId = $currentSlideId;
        $this->retellingCopyId = $retellingCopyId;
        $this->retellingName = $retellingName;
        $this->userId = $userId;
        $this->retellingRequired = $retellingRequired;
    }

    public function getStoryId(): int
    {
        return $this->storyId;
    }

    public function getCurrentSlideId(): int
    {
        return $this->currentSlideId;
    }

    public function getRetellingCopyId(): UuidInterface
    {
        return $this->retellingCopyId;
    }

    public function getRetellingName(): string
    {
        return $this->retellingName;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function isRetellingRequired(): bool
    {
        return $this->retellingRequired;
    }
}
