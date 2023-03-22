<?php

declare(strict_types=1);

namespace backend\modules\repetition;

interface RepetitionApiInterface
{
    public function createNextRepetition(int $testId, int $studentId): void;
}
