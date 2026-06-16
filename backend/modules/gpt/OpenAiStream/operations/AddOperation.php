<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream\operations;

final class AddOperation implements JsonSerializableObject
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
            'op' => 'add',
            'path' => $this->path,
            'value' => $this->value,
        ];
    }
}
