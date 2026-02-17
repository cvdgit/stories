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
        $text = strip_tags($text);

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
                    new Fields([new Message('user', $content)]),
                ),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    /**
     * @throws ExitException
     */
    public function actionCreateForTrainerByFragments(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $fragments = $payload['fragments'] ?? [];

        $fragments = array_map(static function (string $text): string {
            $text = strip_tags($text);
            $text = preg_replace('/\s+/', ' ', $text);
            return trim($text);
        }, $fragments);

        $prompt = LlmPrompt::findByKey('create-trainer-story-from-fragments');
        if ($prompt === null) {
            $this->flushError('Промт не найден');
            Yii::$app->end();
        }

        $text = implode(PHP_EOL, array_map(
            static function(string $text): string {
                return '<fragment>' . $text . '</fragment>';
            },
            $fragments,
        ));

        $content = trim(str_replace(['{TEXT}'], [$text], $prompt->prompt));

        try {
            $this->chatEventStream->send(
                "create-trainer-story-from-fragments",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode(
                    new Fields([new Message('user', $content)]),
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
        $text = strip_tags($text);

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
                    new Fields([new Message('user', $content)]),
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

    public function actionForCoverPrompt(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $text = $payload['text'];
        $text = strip_tags($text);
        $content = <<<TEXT
            Есть текст:
            <текст>
            $text
            </текст>
            Сформируй промт для DALL·E-3 что бы сгенерировать обложку, соответсвтующую этому тексту.
            Используй максимально простые и понятные формалировки.
            Верни только промт.
            TEXT;
        try {
            $this->chatEventStream->send(
                "story-for-cover-prompt",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode(
                    new Fields([new Message('user', $content)]),
                ),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionSpeechTrainerSentences(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $text = $payload['text'];
        $text = strip_tags($text);

        if (!$text) {
            $this->flushError('no text');
            Yii::$app->end();
        }

        $prompt = LlmPrompt::findByKey('speech-trainer-sentences');
        if ($prompt === null) {
            $this->flushError('Промт не найден');
            Yii::$app->end();
        }

        $content = trim(str_replace(['{TEXT}'], [$text], $prompt->prompt));

        try {
            $this->chatEventStream->send(
                "speech-trainer-sentences",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode(
                    new Fields([new Message('user', $content)]),
                ),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }
}
