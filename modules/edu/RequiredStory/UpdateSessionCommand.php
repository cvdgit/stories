<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory;

use DateTimeInterface;

final class UpdateSessionCommand
{
    /**
     * @var int
     */
    private $studentId;
    /**
     * @var int
     */
    private $storyId;
    /**
     * @var DateTimeInterface
     */
    private $date;

    public function __construct(int $studentId, int $storyId, DateTimeInterface $date)
    {
        $this->studentId = $studentId;
        $this->storyId = $storyId;
        $this->date = $date;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function getStoryId(): int
    {
        return $this->storyId;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
