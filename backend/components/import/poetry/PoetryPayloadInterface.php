<?php

declare(strict_types=1);

namespace backend\components\import\poetry;

use common\models\TestWord;

interface PoetryPayloadInterface
{
    /**
     * @param iterable<TestWord> $words
     */
    public function createPayload(array $words): array;
}
