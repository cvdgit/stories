<?php

declare(strict_types=1);

namespace frontend\events;

class StudentTestingFinish
{
    private $testingId;
    private $studentId;

    public function __construct(int $testingId, int $studentId)
    {
        $this->testingId = $testingId;
        $this->studentId = $studentId;
    }

    /**
     * @return int
     */
    public function getTestingId(): int
    {
        return $this->testingId;
    }

    /**
     * @return int
     */
    public function getStudentId(): int
    {
        return $this->studentId;
    }
}
