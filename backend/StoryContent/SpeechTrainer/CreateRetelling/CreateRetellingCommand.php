<?php

declare(strict_types=1);

namespace backend\StoryContent\SpeechTrainer\CreateRetelling;

class CreateRetellingCommand
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
     * @var int
     */
    private $currentSlideNumber;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var bool
     */
    private $required;

    public function __construct(
        int $storyId,
        int $currentSlideId,
        int $currentSlideNumber,
        int $userId,
        bool $required = false
    ) {
        $this->storyId = $storyId;
        $this->currentSlideId = $currentSlideId;
        $this->currentSlideNumber = $currentSlideNumber;
        $this->userId = $userId;
        $this->required = $required;
    }

    public function getStoryId(): int
    {
        return $this->storyId;
    }

    public function getCurrentSlideId(): int
    {
        return $this->currentSlideId;
    }

    public function getCurrentSlideNumber(): int
    {
        return $this->currentSlideNumber;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
