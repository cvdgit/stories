<?php

declare(strict_types=1);

namespace frontend\modules\repetition;

use api\modules\v1\RepetitionApiInterface;
use frontend\modules\repetition\Testing\StudentRepetitionSearch;
use yii\data\DataProviderInterface;

class ApiModuleProvider implements RepetitionApiInterface
{
    public function getRepetitionDataProvider(int $studentId): DataProviderInterface
    {
        return (new StudentRepetitionSearch())->search($studentId);
    }
}
