<?php

declare(strict_types=1);

namespace backend\components\book\blocks;

class Test implements GuestBlockInterface
{
    private $testId;
    private $header;
    private $description;

    public function __construct(int $testId, string $header = '', string $description = '')
    {
        $this->testId = $testId;
        $this->header = $header;
        $this->description = $description;
    }

    public function getTestId(): int
    {
        return $this->testId;
    }

    public function isEmpty(): bool
    {
        return empty($this->testId);
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
