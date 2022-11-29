<?php

declare(strict_types=1);

namespace backend\components\import;

class QuestionDto
{
    /** @var string */
    private $name;
    /** @var string */
    private $payload;
    private $answers = [];

    public function __construct(string $name, string $payload = null)
    {
        $this->name = $name;
        $this->payload = $payload;
    }

    /**
     * @param array $answers
     */
    public function setAnswers(array $answers): void
    {
        $this->answers = $answers;
    }

    public function addAnswer(AnswerDto $answer): void
    {
        $this->answers[] = $answer;
    }

    public function createAnswer(string $name, bool $correct, string $description = null): AnswerDto
    {
        $answer = new AnswerDto($name, $correct, $description);
        $this->addAnswer($answer);
        return $answer;
    }

    public function getAnswersCount(): int
    {
        return count($this->answers);
    }

    public function shuffleAnswers(): self
    {
        shuffle($this->answers);
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function getCorrectAnswers(): array
    {
        return array_values(array_filter($this->getAnswers(), static function (AnswerDto $answer) {
            return $answer->isCorrect();
        }));
    }

    /**
     * @return string|null
     */
    public function getPayload(): ?string
    {
        return $this->payload;
    }
}
