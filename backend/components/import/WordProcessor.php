<?php

declare(strict_types=1);

namespace backend\components\import;

use common\models\TestWord;

interface WordProcessor
{
    /**
     * @param TestWord $word
     * @return QuestionDto
     */
    public function process(TestWord $word): QuestionDto;
}
