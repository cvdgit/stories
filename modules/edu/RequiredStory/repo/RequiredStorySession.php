<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\repo;

use Ramsey\Uuid\UuidInterface;

class RequiredStorySession
{
    /**
     * @var UuidInterface
     */
    private $requiredStoryId;
    /**
     * @var string
     */
    private $date;
    /**
     * @var int
     */
    private $plan;
    /**
     * @var int
     */
    private $fact;

    public function __construct(UuidInterface $requiredStoryId, string $date, int $plan, int $fact = 0)
    {
        $this->requiredStoryId = $requiredStoryId;
        $this->date = $date;
        $this->plan = $plan;
        $this->fact = $fact;
    }

    public function getRequiredStoryId(): UuidInterface
    {
        return $this->requiredStoryId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getPlan(): int
    {
        return $this->plan;
    }

    public function getFact(): int
    {
        return $this->fact;
    }

    public function setFact(int $fact): void
    {
        $this->fact = $fact;
    }

    public function isCompleted(): bool
    {
        return $this->fact >= $this->plan;
    }
}
