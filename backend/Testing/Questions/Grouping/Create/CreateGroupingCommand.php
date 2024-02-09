<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Grouping\Create;

class CreateGroupingCommand
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

    public function __construct(int $testId, string $name, string $payload)
    {
        $this->testId = $testId;
        $this->name = $name;
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

    public function getPayload(): string
    {
        return $this->payload;
    }
}
