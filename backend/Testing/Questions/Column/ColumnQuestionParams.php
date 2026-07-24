<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Column;

use JsonSerializable;

class ColumnQuestionParams implements JsonSerializable
{
    /**
     * @var bool
     */
    private $isCorrectAnswerDelay;

    public function __construct(bool $isCorrectAnswerDelay)
    {
        $this->isCorrectAnswerDelay = $isCorrectAnswerDelay;
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            $payload['isCorrectAnswerDelay'] ?? false,
        );
    }

    public function asArray(): array
    {
        return [
            'isCorrectAnswerDelay' => $this->isCorrectAnswerDelay,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    public function withShowCorrectAnswer(bool $value): self
    {
        $obj = clone $this;
        $obj->isCorrectAnswerDelay = $value;
        return $obj;
    }

    public function isCorrectAnswerDelay(): bool
    {
        return $this->isCorrectAnswerDelay;
    }
}
