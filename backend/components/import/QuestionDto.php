<?php

namespace backend\components\import;

class QuestionDto
{

    private $name;
    private $answers = [];

    public function __construct(string $name)
    {
        $this->name = $name;
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

    public function createAnswer(string $name, bool $correct): AnswerDto
    {
        $answer = new AnswerDto($name, $correct);
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
}