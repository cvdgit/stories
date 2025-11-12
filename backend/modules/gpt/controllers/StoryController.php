<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use backend\modules\gpt\ChatEventStream;
use backend\modules\gpt\Message\Fields;
use backend\modules\gpt\Message\Message;
use backend\modules\gpt\Prompts\LlmPrompt;
use Exception;
use Yii;
use yii\base\ExitException;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;

class StoryController extends Controller
{
    use StreamTrait;

    /**
     * @var ChatEventStream
     */
    private $chatEventStream;

    public function __construct($id, $module, ChatEventStream $chatEventStream, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->chatEventStream = $chatEventStream;
    }

    /**
     * @throws ExitException
     */
    public function actionCreateForTrainer(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $text = $payload['text'];

        $prompt = LlmPrompt::findByKey('create-trainer-story-from-text');
        if ($prompt === null) {
            $this->flushError('Промт не найден');
            Yii::$app->end();
        }

        $content = trim(str_replace(['{TEXT}'], [$text], $prompt->prompt));

        try {
            $this->chatEventStream->send(
                "create-trainer-story-from-text",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode(
                    new Fields([new Message('user', $content)])
                ),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    /**
     * @throws ExitException
     */
    public function actionCreate(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $text = $payload['text'];

        $prompt = LlmPrompt::findByKey('create-story-from-text');
        if ($prompt === null) {
            $this->flushError('Промт не найден');
            Yii::$app->end();
        }

        $content = trim(str_replace(['{TEXT}'], [$text], $prompt->prompt));

        try {
            $this->chatEventStream->send(
                "create-story-from-text",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode(
                    new Fields([new Message('user', $content)])
                ),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    private function flushError(string $text): void
    {
        echo "event: error\n";
        $ops = [
            "ops" => [
                [
                    "op" => "replace",
                    "path" => "",
                    "value" => [
                        "error_text" => $text,
                    ],
                ],
            ],
        ];
        echo 'data: ' . Json::encode($ops) . "\n\n";
        flush();
    }
}
