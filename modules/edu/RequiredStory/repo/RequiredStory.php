<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use yii\helpers\Json;

class RequiredStory
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
    private $createdAt;
    /**
     * @var int
     */
    private $createdBy;
    /**
     * @var DateTimeInterface
     */
    private $startedAt;
    /**
     * @var int
     */
    private $days;
    /**
     * @var RequiredStoryStatus
     */
    private $status;
    /**
     * @var RequiredStoryMetadata
     */
    private $metadata;

    public function __construct(
        UuidInterface $id,
        int $storyId,
        int $studentId,
        DateTimeInterface $createdAt,
        int $createdBy,
        DateTimeInterface $startedAt,
        int $days,
        RequiredStoryStatus $status,
        RequiredStoryMetadata $metadata
    ) {
        $this->id = $id;
        $this->storyId = $storyId;
        $this->studentId = $studentId;
        $this->createdAt = $createdAt;
        $this->createdBy = $createdBy;
        $this->startedAt = $startedAt;
        $this->days = $days;
        $this->status = $status;
        $this->metadata = $metadata;
    }

    /**
     * @throws Exception
     */
    public static function fromArray(array $array): self
    {
        return new self(
            Uuid::fromString($array['id']),
            (int) $array['story_id'],
            (int) $array['student_id'],
            new DateTimeImmutable('@' . $array['created_at']),
            (int) $array['created_by'],
            new DateTimeImmutable('@' . $array['started_at']),
            (int) $array['days'],
            new RequiredStoryStatus($array['status']),
            RequiredStoryMetadata::fromArray(Json::decode($array['metadata'])),
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

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getStartedAt(): DateTimeInterface
    {
        return $this->startedAt;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function getStatus(): RequiredStoryStatus
    {
        return $this->status;
    }

    public function getMetadata(): RequiredStoryMetadata
    {
        return $this->metadata;
    }

    public function update(
        int $storyId,
        int $studentId,
        DateTimeInterface $startedAt,
        int $days,
        RequiredStoryStatus $status,
        RequiredStoryMetadata $metadata
    ): void {
        $this->storyId = $storyId;
        $this->studentId = $studentId;
        $this->startedAt = $startedAt;
        $this->days = $days;
        $this->status = $status;
        $this->metadata = $metadata;
    }

    /*public function calcStatus(int $total, int $fact): void
    {
        $this->status = RequiredStoryStatus::open();
        if ($fact >= $total) {
            $this->status = RequiredStoryStatus::close();
        }
    }*/
}
