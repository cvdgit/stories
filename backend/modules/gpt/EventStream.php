<?php

declare(strict_types=1);

namespace backend\modules\gpt;

use yii\web\HttpException;

class EventStream
{
    /**
     * @throws HttpException
     */
    public function send(string $url, string $fieldsJson): void
    {
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $fieldsJson,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: text/event-stream"
            ],
            CURLOPT_WRITEFUNCTION => function ($ch, $chunk) {
                echo $chunk;
                flush();
                return strlen($chunk);
            },
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        curl_exec($ch);

        $error = curl_error($ch);
        curl_close($ch);

        if ($error !== "") {
            throw new HttpException(500, $error);
        }
    }
}
