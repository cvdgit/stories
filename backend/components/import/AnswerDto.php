<?php

namespace backend\components\import;

class AnswerDto
{

    private $name;
    private $correct;

    public function __construct(string $name, bool $correct)
    {
        $this->name = $name;
        $this->correct = $correct;
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
}
