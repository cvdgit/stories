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

    public function __construct(
        UuidInterface $id,
        string $storyTitle,
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
    }

    /**
     * @throws Exception
     */
    public static function fromArray(array $row): self
    {
        return new self(
            Uuid::fromString($row['id']),
            $row['storyTitle'],
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
}
