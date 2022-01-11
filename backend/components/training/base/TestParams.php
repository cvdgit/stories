<?php

namespace backend\components\training\base;

class TestParams
{

    private $id;
    private $source;
    private $incorrectAnswerText;

    public function __construct(int $id, int $source, string $incorrectAnswerText)
    {
        $this->id = $id;
        $this->source = $source;
        $this->incorrectAnswerText = $incorrectAnswerText;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSource(): int
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getIncorrectAnswerText(): string
    {
        return $this->incorrectAnswerText;
    }
}