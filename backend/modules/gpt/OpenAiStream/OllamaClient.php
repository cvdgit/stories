<?php

declare(strict_types=1);

namespace backend\modules\gpt\OpenAiStream;

use Exception;
use RuntimeException;

final class OllamaClient
{
    /**
     * @throws Exception
     */
    public function stream(
        string $url,
        array $payload,
        callable $onChunk
    ): void {
        $buffer = '';

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            CURLOPT_WRITEFUNCTION => function (
                $curl,
                string $chunk
            ) use (
                &$buffer,
                $onChunk
            ) {

                $buffer .= $chunk;

                while (($pos = strpos($buffer, "\n")) !== false) {

                    $line = substr(
                        $buffer,
                        0,
                        $pos
                    );

                    $buffer = substr(
                        $buffer,
                        $pos + 1
                    );

                    $line = trim($line);

                    if (
                        $line === '' ||
                        !str_starts_with($line, 'data:')
                    ) {
                        continue;
                    }

                    $payload = trim(
                        substr($line, 5)
                    );

                    if ($payload === '[DONE]') {
                        continue;
                    }

                    $json = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

                    if ($json) {
                        $onChunk($json);
                    }
                }

                return strlen($chunk);
            }
        ]);

        curl_exec($ch);

        if ($error = curl_error($ch)) {
            throw new RuntimeException($error);
        }

        curl_close($ch);
    }
}
