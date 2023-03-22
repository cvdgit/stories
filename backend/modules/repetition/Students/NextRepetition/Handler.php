<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students\NextRepetition;

use backend\modules\repetition\RepetitionApiInterface;

class Handler
{
    private $repetitionApi;

    public function __construct(RepetitionApiInterface $repetitionApi)
    {
        $this->repetitionApi = $repetitionApi;
    }

    public function handle(NextRepetitionForm $command): void
    {
        $this->repetitionApi->createNextRepetition((int)$command->test_id, (int)$command->student_id);
    }
}
