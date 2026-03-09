<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\Create;

use DateTimeInterface;
use modules\edu\RequiredStory\repo\RequiredStoryMetadata;
use modules\edu\RequiredStory\repo\RequiredStoryStatus;
use Ramsey\Uuid\UuidInterface;

class CreateRequiredStoryCommand
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
     * @var int
     */
    private $studentId;
    /**
     * @var DateTimeInterface
     */
    private $startDate;
    /**
     * @var int
     */
    private $days;
    /**
     * @var RequiredStoryMetadata
     */
    private $metadata;
    /**
     * @var int
     */
    private $createdBy;
    /**
     * @var RequiredStoryStatus
     */
    private $status;

    public function __construct(
        UuidInterface $id,
        int $storyId,
        int $studentId,
        int $createdBy,
        DateTimeInterface $startDate,
        int $days,
        RequiredStoryStatus $status,
        RequiredStoryMetadata $metadata
    ) {
        $this->id = $id;
        $this->storyId = $storyId;
        $this->studentId = $studentId;
        $this->startDate = $startDate;
        $this->days = $days;
        $this->metadata = $metadata;
        $this->createdBy = $createdBy;
        $this->status = $status;
    }

    public function getStoryId(): int
    {
        return $this->storyId;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function getMetadata(): RequiredStoryMetadata
    {
        return $this->metadata;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getStatus(): RequiredStoryStatus
    {
        return $this->status;
    }
}
