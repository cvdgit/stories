<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream\operations;

interface JsonSerializableObject
{
    public function toArray(): array;
}
