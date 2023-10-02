<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Create;

class CreateQuestionCommand
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
    private $image;
    /**
     * @var int
     */
    private $maxPrevItems;

    public function __construct(int $testId, string $name, string $image, int $maxPrevItems)
    {
        $this->testId = $testId;
        $this->name = $name;
        $this->image = $image;
        $this->maxPrevItems = $maxPrevItems;
    }

    public function getTestId(): int
    {
        return $this->testId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getMaxPrevItems(): int
    {
        return $this->maxPrevItems;
    }
}
