<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use backend\modules\gpt\Prompts\LlmPrompt;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\ExitException;
use yii\helpers\Json;
use yii\web\Request;

class MentalMapController extends BaseStreamController
{
    public function actionTextFragments(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $text = $payload['text'];

        $content = <<<TEXT
Ты разбиваешь текст на фрагменты.
<текст>
$text
</текст>

Разбей весь текст на фрагменты по смыслу не больше 500 символов на фрагмент.
Ничего не убирай.
Сохраняй переносы строк \r\n
Сохраняй стихотворный формат.
ВАЖНО: не возвращай пустые фрагменты!

Ответь в формате json.
Пример: ["текст фрагмента 1", "текст фрагмента 2"]
TEXT;

        $fields = $this->createFieldsPayload($content);
        $this->sendStream('text-create-fragments', Json::encode($fields));
    }

    /**
     * @throws ExitException
     */
    public function actionFragmentResult(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $text = $payload['text'];
        $userResponse = $payload['userResponse'];

        $prompt = LlmPrompt::findByKey('mental-map-tree-presentation-fragment');
        if ($prompt === null) {
            $this->flushError('Промт не найден');
            Yii::$app->end();
        }

        $content = str_replace(
            ['{TEXT}', '{USER_RESPONSE}'],
            [$text, $userResponse],
            $prompt->prompt
        );

        $fields = $this->createFieldsPayload($content);
        $this->sendStream('mental-map-presentation-result', Json::encode($fields));
    }

    /**
     * @throws \JsonException
     */
    public function actionChat(Request $request): void
    {
        $payload = json_decode($request->getRawBody(), true, 512, JSON_THROW_ON_ERROR);

        $fields = [
            "input" => [
                "messages" => $payload['input']['messages'],
            ],
            "config" => [
                "metadata" => [
                    "conversation_id" => Uuid::uuid4()->toString(),
                ],
            ],
            "include_names" => [],
        ];

        $this->sendStream(
            'mental-map-chat',
            Json::encode($fields)
        );
    }
}
