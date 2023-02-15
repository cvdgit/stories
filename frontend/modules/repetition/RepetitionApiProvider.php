<?php

declare(strict_types=1);

namespace frontend\modules\repetition;

use frontend\modules\repetition\Testing\StudentRepetitionSearch;
use modules\edu\RepetitionApiInterface;
use yii\data\DataProviderInterface;

class RepetitionApiProvider implements RepetitionApiInterface
{
    public function getRepetitionDataProvider(int $studentId): DataProviderInterface
    {
        return (new StudentRepetitionSearch())->search($studentId);
    }
}
