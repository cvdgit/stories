<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream\operations;

final class ChainState implements JsonSerializableObject
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'streamed_output' => [],
            'final_output' => null,
            'logs' => new \stdClass(),
            'name' => '/completions',
            'type' => 'chain',
        ];
    }
}
