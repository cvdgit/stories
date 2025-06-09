<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Math\Create;

class CreateMathQuestionCommand
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
     * @var array
     */
    private $answers;

    public function __construct(int $testId, string $name, string $payload, array $answers)
    {
        $this->testId = $testId;
        $this->name = $name;
        $this->payload = $payload;
        $this->answers = $answers;
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

    public function getAnswers(): array
    {
        return $this->answers;
    }
}
