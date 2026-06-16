<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream\operations;

final class StreamLogEvent implements JsonSerializableObject
{
    /**
     * @param JsonSerializableObject[] $operations
     */
    private $operations;

    public function __construct(array $operations)
    {
        $this->operations = $operations;
    }

    public function toArray(): array
    {
        return [
            'ops' => array_map(
                static function (JsonSerializableObject $op) {
                    return $op->toArray();
                },
                $this->operations,
            ),
        ];
    }
}
