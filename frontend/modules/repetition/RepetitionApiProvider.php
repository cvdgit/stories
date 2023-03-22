<?php

declare(strict_types=1);

namespace frontend\modules\repetition;

use frontend\modules\repetition\Finish\FinishForm;
use frontend\modules\repetition\Finish\FinishHandler;
use frontend\modules\repetition\Testing\StudentRepetitionSearch;
use modules\edu\RepetitionApiInterface;
use yii\data\DataProviderInterface;

class RepetitionApiProvider implements RepetitionApiInterface, \backend\modules\repetition\RepetitionApiInterface
{
    private $finishHandler;

    public function __construct(FinishHandler $finishHandler)
    {
        $this->finishHandler = $finishHandler;
    }

    public function getRepetitionDataProvider(int $studentId): DataProviderInterface
    {
        return (new StudentRepetitionSearch())->search($studentId);
    }

    public function createNextRepetition(int $testId, int $studentId): void
    {
        $command = new FinishForm([
            'test_id' => $testId,
            'student_id' => $studentId,
        ]);
        if (!$command->validate()) {
            throw new \DomainException('Command not valid');
        }
        $this->finishHandler->handle($command);
    }
}
