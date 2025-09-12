<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Column\Create;

use backend\Testing\Questions\Column\ColumnQuestionPayload;

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
     * @var ColumnQuestionPayload
     */
    private $payload;

    public function __construct(int $testId, string $name, string $answerName, ColumnQuestionPayload $payload)
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

    public function getPayload(): ColumnQuestionPayload
    {
        return $this->payload;
    }
}
