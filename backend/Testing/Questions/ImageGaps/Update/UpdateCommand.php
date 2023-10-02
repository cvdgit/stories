<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Update;

class UpdateCommand
{
    /**
     * @var int
     */
    private $questionId;
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
    private $maxPrevItems;

    public function __construct(int $questionId, string $name, string $payload, int $maxPrevItems)
    {
        $this->questionId = $questionId;
        $this->name = $name;
        $this->payload = $payload;
        $this->maxPrevItems = $maxPrevItems;
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getMaxPrevItems(): int
    {
        return $this->maxPrevItems;
    }
}
