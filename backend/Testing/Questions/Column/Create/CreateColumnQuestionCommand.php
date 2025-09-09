<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Column\Create;

class CreateColumnQuestionCommand
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
    private $answerName;
    /**
     * @var array
     */
    private $payload;

    public function __construct(int $testId, string $name, string $answerName, array $payload)
    {
        $this->testId = $testId;
        $this->name = $name;
        $this->answerName = $answerName;
        $this->payload = $payload;
    }

    public function getTestId(): int
    {
        return $this->testId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAnswerName(): string
    {
        return $this->answerName;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
