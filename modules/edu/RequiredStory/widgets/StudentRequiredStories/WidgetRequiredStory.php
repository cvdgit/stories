<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\widgets\StudentRequiredStories;

use common\components\StoryCover;
use modules\edu\RequiredStory\repo\RequiredStorySession;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class WidgetRequiredStory
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var int
     */
    private $storyId;
    /**
     * @var string
     */
    private $storyTitle;
    /**
     * @var string
     */
    private $storyCover;
    /**
     * @var RequiredStorySession|null
     */
    private $session;
    /**
     * @var int
     */
    private $priority;

    private function __construct(
        UuidInterface $id,
        int $storyId,
        string $storyTitle,
        string $storyCover,
        int $priority
    ) {
        $this->id = $id;
        $this->storyId = $storyId;
        $this->storyTitle = $storyTitle;
        $this->storyCover = $storyCover;
        $this->priority = $priority;
    }

    public static function fromArray(array $array): self
    {
        return new self(
            Uuid::fromString($array['id']),
            (int) $array['storyId'],
            $array['storyTitle'],
            StoryCover::getListThumbPath($array['storyCover']),
            (int) $array['priority'],
        );
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getStoryId(): int
    {
        return $this->storyId;
    }

    public function getStoryTitle(): string
    {
        return $this->storyTitle;
    }

    public function getStoryCover(): string
    {
        return $this->storyCover;
    }

    public function getSession(): ?RequiredStorySession
    {
        return $this->session;
    }

    public function setSession(RequiredStorySession $session): void
    {
        $this->session = $session;
    }

    public function isPriority(): bool
    {
        return $this->priority > 0;
    }
}
