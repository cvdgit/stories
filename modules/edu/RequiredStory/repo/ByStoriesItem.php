<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

class ByStoriesItem
{
    /**
     * @var int
     */
    private $storyId;
    /**
     * @var array
     */
    private $studentIds;
    /**
     * @var string
     */
    private $storyTitle;
    /**
     * @var array
     */
    private $studentNames;

    public function __construct(int $storyId, string $storyTitle, array $studentIds, array $studentNames)
    {
        $this->storyId = $storyId;
        $this->studentIds = array_map('intval', $studentIds);
        $this->storyTitle = $storyTitle;
        $this->studentNames = $studentNames;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['storyId'],
            $row['storyTitle'],
            explode(',', $row['studentIds']),
            explode(',', $row['studentNames']),
        );
    }

    public function getStoryId(): int
    {
        return $this->storyId;
    }

    public function getStudentIds(): array
    {
        return $this->studentIds;
    }

    public function getStoryTitle(): string
    {
        return $this->storyTitle;
    }

    public function getStudentNames(): array
    {
        return $this->studentNames;
    }
}
