<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream;

use backend\modules\gpt\OpenAiStream\operations\JsonSerializableObject;
use Exception;

final class LangChainStreamEmitter
{
    /**
     * @throws Exception
     */
    public function send(JsonSerializableObject $event): void
    {
        echo "event: data\n";

        echo "data: " . json_encode(
                $event->toArray(),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES,
            );

        echo "\n\n";

        flush();
    }

    public function end(): void
    {
        echo "event: end\n\n";
        flush();
    }
}
