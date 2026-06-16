<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream;

use backend\modules\gpt\OpenAiStream\operations\AddOperation;
use backend\modules\gpt\OpenAiStream\operations\ReplaceOperation;
use backend\modules\gpt\OpenAiStream\operations\StreamLogEvent;
use Exception;

final class StreamProcessor
{
    private $fullText = '';

    /**
     * @throws Exception
     */
    public function process(
        array $chunk,
        LangChainStreamEmitter $emitter
    ): void {

        $delta =
            $chunk['choices'][0]['delta']
            ?? [];

        if (!isset($delta['content'])) {
            return;
        }

        $token = $delta['content'];

        if ($token === '') {
            return;
        }

        $this->fullText .= $token;

        $emitter->send(
            new StreamLogEvent([
                new AddOperation(
                    '/streamed_output/-',
                    $token
                ),
                new ReplaceOperation(
                    '/final_output',
                    $this->fullText
                ),
            ])
        );
    }

    public function getResponse(): string
    {
        return $this->fullText;
    }
}
