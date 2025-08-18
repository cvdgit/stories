<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Step\Create;

use backend\Testing\Questions\Step\StepPayload;

class CreateStepQuestionCommand
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
     * @var array<StepPayload>
     */
    private $steps;
    /**
     * @var string
     */
    private $job;

    public function __construct(int $testId, string $name, string $job, array $steps)
    {
        $this->testId = $testId;
        $this->name = $name;
        $this->job = $job;
        $this->steps = $steps;
    }

    public function getTestId(): int
    {
        return $this->testId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getJob(): string
    {
        return $this->job;
    }
}
