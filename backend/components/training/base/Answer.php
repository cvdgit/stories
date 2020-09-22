<?php

namespace backend\components\training\base;

class Answer
{

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var mixed|null */
    private $image;

    /** @var bool */
    private $correct;

    public function __construct(int $id, string $name, bool $correct, $image = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->correct = $correct;
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->correct;
    }

}