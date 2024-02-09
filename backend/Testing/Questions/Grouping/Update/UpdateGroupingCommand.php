<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Grouping\Update;

class UpdateGroupingCommand
{
    /**
     * @var int
     */
    private $testId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $payload;
    /**
     * @var int
     */
    private $questionId;

    public function __construct(int $testId, int $questionId, string $name, string $payload)
    {
        $this->testId = $testId;
        $this->name = $name;
        $this->payload = $payload;
        $this->questionId = $questionId;
    }

    public function getTestId(): int
    {
        return $this->testId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }
}
