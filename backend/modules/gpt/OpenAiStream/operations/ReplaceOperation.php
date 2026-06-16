<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream\operations;

final class ReplaceOperation implements JsonSerializableObject
{
    private $path;
    private $value;

    public function __construct(string $path, $value)
    {
        $this->path = $path;
        $this->value = $value;
    }

    public function toArray(): array
    {
        return [
            'op' => 'replace',
            'path' => $this->path,
            'value' => $this->value,
        ];
    }
}
