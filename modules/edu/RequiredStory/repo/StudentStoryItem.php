<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class StudentStoryItem
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var int
     */
    private $storyId;

    public function __construct(UuidInterface $id, int $storyId)
    {
        $this->id = $id;
        $this->storyId = $storyId;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            Uuid::fromString($row['id']),
            (int) $row['storyId'],
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
}
