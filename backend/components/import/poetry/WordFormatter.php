<?php

declare(strict_types=1);

namespace backend\components\import\poetry;

use common\models\TestWord;
use yii\base\InvalidArgumentException;

class WordFormatter
{
    /**
     * @param iterable<TestWord> $words
     * @return iterable<TestWord>
     */
    public function formatWords(array $words, int $linePerQuestion): array
    {
        if ($linePerQuestion === 0) {
            throw new InvalidArgumentException('linePerQuestion не может быть 0');
        }

        $questions = [];
        $num = ceil(count($words) / $linePerQuestion);
        for ($i = 0; $i < $num; $i++) {
            $questionWords = array_slice($words, $i * $linePerQuestion, $linePerQuestion);
            $questions[] = $questionWords;
        }
        return $questions;
    }
}
