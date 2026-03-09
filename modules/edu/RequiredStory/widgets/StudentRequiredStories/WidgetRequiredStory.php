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

    private function __construct(
        UuidInterface $id,
        int $storyId,
        string $storyTitle,
        string $storyCover
    ) {
        $this->id = $id;
        $this->storyId = $storyId;
        $this->storyTitle = $storyTitle;
        $this->storyCover = $storyCover;
    }

    public static function fromArray(array $array): self
    {
        return new self(
            Uuid::fromString($array['id']),
            (int) $array['storyId'],
            $array['storyTitle'],
            StoryCover::getListThumbPath($array['storyCover']),
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
}
