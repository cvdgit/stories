<?php

namespace backend\components\import;

class AnswerDto
{

    private $name;
    private $correct;
    private $description;

    public function __construct(string $name, bool $correct, string $description = null)
    {
        $this->name = $name;
        $this->correct = $correct;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->correct;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
