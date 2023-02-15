<?php

declare(strict_types=1);

namespace modules\edu;

use yii\data\DataProviderInterface;

interface RepetitionApiInterface
{
    public function getRepetitionDataProvider(int $studentId): DataProviderInterface;
}
