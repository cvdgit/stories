<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Math;

class MathPayload
{
    /**
     * @var string
     */
    private $job;
    /**
     * @var array
     */
    private $answers;
    /**
     * @var array
     */
    private $fragments;
    /**
     * @var bool
     */
    private $isInputAnswer;
    /**
     * @var bool
     */
    private $isGapsQuestion;

    public function __construct(
        string $job,
        array $answers,
        array $fragments,
        bool $isInputAnswer,
        bool $isGapsQuestion
    ) {
        $this->job = $job;
        $this->answers = $answers;
        $this->fragments = $fragments;
        $this->isInputAnswer = $isInputAnswer;
        $this->isGapsQuestion = $isGapsQuestion;
    }

    public function getJob(): string
    {
        return $this->job;
    }

    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function isInputAnswer(): bool
    {
        return $this->isInputAnswer;
    }

    public function isGapsQuestion(): bool
    {
        return $this->isGapsQuestion;
    }

    public function asArray(): array
    {
        return [
            'job' => $this->job,
            'answers' => $this->answers,
            'fragments' => $this->fragments,
            'isInputAnswer' => $this->isInputAnswer,
            'isGapsQuestion' => $this->isGapsQuestion,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['job'],
            $payload['answers'] ?? [],
            $payload['fragments'] ?? [],
            $payload['isInputAnswer'] ?? false,
            $payload['isGapsQuestion'] ?? false,
        );
    }

    public function getFragments(): array
    {
        return $this->fragments;
    }
}
