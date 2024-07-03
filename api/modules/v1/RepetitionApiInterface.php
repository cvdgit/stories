<?php

declare(strict_types=1);

namespace api\modules\v1;

use yii\data\DataProviderInterface;

interface RepetitionApiInterface
{
    public function getRepetitionDataProvider(int $studentId): DataProviderInterface;
}
