<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Math\Create;

use backend\Testing\Questions\Math\MathPayload;

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
     * @var MathPayload
     */
    private $payload;

    public function __construct(int $testId, string $name, MathPayload $payload)
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

    public function getPayload(): MathPayload
    {
        return $this->payload;
    }
}
