<?php

declare(strict_types=1);

namespace frontend\MentalMap\repetition;

final class StartMentalMapRepetitionCommand
{
    /**
     * @var string
     */
    private $mentalMapId;
    /**
     * @var int
     */
    private $studentId;

    public function __construct(string $mentalMapId, int $studentId)
    {
        $this->mentalMapId = $mentalMapId;
        $this->studentId = $studentId;
    }

    public function getMentalMapId(): string
    {
        return $this->mentalMapId;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }
}
