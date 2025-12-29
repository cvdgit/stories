<?php

declare(strict_types=1);

namespace frontend\events;

final class RestartTestEvent
{
    /**
     * @var int
     */
    private $testId;
    /**
     * @var int
     */
    private $studentId;

    public function __construct(int $testId, int $studentId)
    {
        $this->testId = $testId;
        $this->studentId = $studentId;
    }

    public function getTestId(): int
    {
        return $this->testId;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }
}
