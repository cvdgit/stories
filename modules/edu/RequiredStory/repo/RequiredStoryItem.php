<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class RequiredStoryItem
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $storyTitle;
    /**
     * @var string
     */
    private $studentName;
    /**
     * @var DateTimeInterface
     */
    private $startedDate;
    /**
     * @var DateTimeInterface
     */
    private $createdDate;
    /**
     * @var RequiredStoryStatus
     */
    private $status;
    /**
     * @var int
     */
    private $storyId;
    /**
     * @var int
     */
    private $studentId;

    public function __construct(
        UuidInterface $id,
        int $storyId,
        string $storyTitle,
        int $studentId,
        string $studentName,
        DateTimeInterface $startedDate,
        DateTimeInterface $createdDate,
        RequiredStoryStatus $status
    ) {
        $this->id = $id;
        $this->storyTitle = $storyTitle;
        $this->studentName = $studentName;
        $this->startedDate = $startedDate;
        $this->createdDate = $createdDate;
        $this->status = $status;
        $this->storyId = $storyId;
        $this->studentId = $studentId;
    }

    /**
     * @throws Exception
     */
    public static function fromArray(array $row): self
    {
        return new self(
            Uuid::fromString($row['id']),
            (int) $row['storyId'],
            $row['storyTitle'],
            (int) $row['studentId'],
            $row['studentName'],
            (new DateTimeImmutable('@' . $row['startedAt'])),
            (new DateTimeImmutable('@' . $row['createdAt'])),
            new RequiredStoryStatus($row['status'])
        );
    }

    public function getStoryTitle(): string
    {
        return $this->storyTitle;
    }

    public function getStudentName(): string
    {
        return $this->studentName;
    }

    public function getStartedDate(): DateTimeInterface
    {
        return $this->startedDate;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCreatedDate(): DateTimeInterface
    {
        return $this->createdDate;
    }

    public function getStatus(): RequiredStoryStatus
    {
        return $this->status;
    }

    public function getStoryId(): int
    {
        return $this->storyId;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }
}
