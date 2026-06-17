<?php

declare(strict_types=1);

namespace backend\modules\gpt;

use backend\modules\gpt\OpenAiStream\LangChainStreamEmitter;
use backend\modules\gpt\OpenAiStream\LlmFeedbackLogger;
use backend\modules\gpt\OpenAiStream\OllamaClient;
use backend\modules\gpt\OpenAiStream\operations\AddOperation;
use backend\modules\gpt\OpenAiStream\operations\ChainState;
use backend\modules\gpt\OpenAiStream\operations\ReplaceOperation;
use backend\modules\gpt\OpenAiStream\operations\StreamLogEvent;
use backend\modules\gpt\OpenAiStream\StreamProcessor;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\helpers\Json;

class OllamaEventStream implements EventStreamInterface
{
    /**
     * @throws Exception
     */
    public function send(string $target, string $url, string $fieldsJson): void
    {
        $emitter = new LangChainStreamEmitter();

        $emitter->send(
            new StreamLogEvent([
                new ReplaceOperation(
                    '',
                    (new ChainState(
                        $runId = Uuid::uuid4()->toString()
                    ))->toArray()
                )
            ])
        );

        $emitter->send(
            new StreamLogEvent([
                new AddOperation(
                    '/streamed_output/-',
                    ''
                ),
                new ReplaceOperation(
                    '/final_output',
                    ''
                )
            ])
        );

        $processor = new StreamProcessor();
        $logger = new LlmFeedbackLogger();

        $client = new OllamaClient();

        $messages = Json::decode($fieldsJson);
        $messages = $messages['input']['messages'];

        $client->stream(
            $url,
            [
                'model' => 'gpt-oss:20b',
                'messages' => $messages,
                'stream' => true,
                'temperature' => 0,
            ],
            static function (array $chunk) use ($processor, $emitter) {
                $processor->process(
                    $chunk,
                    $emitter
                );
            }
        );

        try {
            $logger->save(
                $runId,
                $target,
                Yii::$app->user->getId(),
                $messages,
                $processor->getResponse()
            );
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
        }

        $emitter->end();
    }
}
