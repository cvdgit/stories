<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use backend\modules\gpt\ChatEventStream;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

class StreamController extends Controller
{
    /**
     * @var ChatEventStream
     */
    private $chatEventStream;

    public function __construct($id, $module, ChatEventStream $chatEventStream, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->chatEventStream = $chatEventStream;
    }

    public function beforeAction($action): bool
    {
        if ($action->id === "stream") {
            $this->enableCsrfValidation = false;
        }

        @ob_end_clean();
        ini_set('output_buffering', '0');
        set_time_limit(0);

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache, must-revalidate");
        header("X-Accel-Buffering: no");
        header("Connection: keep-alive");

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->stream = true;
        $response->isSent = true;
        Yii::$app->session->close();

        return parent::beforeAction($action);
    }

    public function actionChat(Request $request): void
    {
        $text = $request->post("content");
        $role = $request->post("role");
        $questions = $request->post("questions");
        $answers = $request->post("answers");

        $rolesMap = [
            "business_rx" => "Ты бизнес аналитик с большим опытом работы с системой электронного документооборота Directum RX, которой занимается созданием электронных тестов.",
            "systems_rx" => "Ты системный аналитик, спроектировавший множество решений для системы электронного документооборота Directum RX, которой занимается созданием электронных тестов.",
            "history_teacher" => "Ты учитель истории, которой занимается созданием электронных тестов для обучения.",
            "english_teacher" => "Ты школьный учитель английского языка, которой занимается созданием электронных тестов для обучения.",
            "biology_teacher" => "Ты школьный учитель биологии, которой занимается созданием электронных тестов для обучения.",
            "marketer" => "Ты маркетолог с большим опытом, которой занимается созданием электронных тестов для обучения.",
        ];

        $roleText = $rolesMap[$role] ?? "";

        $content = <<<TEXT
$roleText
Проанализируй следующий текст:
```
$text
```
Сформируй $questions вопросов по получившемуся тексту.
Вопросы должны быть просто и понятно сформулированы, иметь один или несколько правильных ответов.
Вопросы должны включать ключевые понятия, основные события, даты, факты и словарные термины.
Придумай подходящие по смыслу неправильные ответы к вопросам, что бы итоговое количество вариантов ответов было не больше, чем $answers
Ответь в формате json.
[{{"question": "текст вопроса",
"answers": [
{{"answer": "текст ответа", "correct": "type boolean, правильный или нет"}}
]
}}]
TEXT;

        $message = [
            "role" => "user",
            "content" => trim($content),
        ];

        $fields = [
            "input" => [
                "messages" => [
                    $message,
                ],
            ],
            "config" => [
                "metadata" => [
                    "conversation_id" => Uuid::uuid4()->toString(),
                ],
            ],
            "include_names" => [],
        ];

        try {
            $this->chatEventStream->send("chat", Yii::$app->params["gpt.api.completions.host"], Json::encode($fields));
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionPassTestChat(Request $request): void
    {
        $prompt = $request->post("prompt");

        if ($prompt) {
            $content = $prompt;
        } else {
            $text = $request->post("content");
            $role = $request->post("role");
            $fragments = $request->post("fragments");

            $rolesMap = [
                "business_rx" => "Ты бизнес аналитик с большим опытом работы с системой электронного документооборота Directum RX, которой занимается созданием электронных тестов.",
                "systems_rx" => "Ты системный аналитик, спроектировавший множество решений для системы электронного документооборота Directum RX, которой занимается созданием электронных тестов.",
                "history_teacher" => "Ты учитель истории, которой занимается созданием электронных тестов для обучения.",
                "english_teacher" => "Ты школьный учитель английского языка, которой занимается созданием электронных тестов для обучения.",
                "biology_teacher" => "Ты школьный учитель биологии, которой занимается созданием электронных тестов для обучения.",
                "marketer" => "Ты маркетолог с большим опытом, которой занимается созданием электронных тестов для обучения.",
            ];

            $roleText = $rolesMap[$role] ?? "";
            $fragmentsPrompt = "";
            if (!empty($fragments)) {
                $fragmentsText = implode(", ", array_map(static function (string $f) {
                    return '"' . $f . '"';
                }, $fragments));
                $fragmentsPrompt = <<<TXT
Исключи слова: $fragmentsText
TXT;
            }

            $content = <<<TEXT
$roleText
Проанализируй следующий текст:
```
$text
```
Выбери из текста слова, которые, на твой взгляд, определяют его суть.
Эти слова не должны идти друг за другом в тексте если это не словосочетание.
Не меняй формы слов. Слова оставляй также, как они написаны в тексте.
$fragmentsPrompt
Ответь в формате json: ["слово"]
TEXT;
        }

        $message = [
            "role" => "user",
            "content" => trim($content),
        ];

        $fields = [
            "input" => [
                "messages" => [
                    $message,
                ],
            ],
            "config" => [
                "metadata" => [
                    "conversation_id" => Uuid::uuid4()->toString(),
                ],
            ],
            "include_names" => [],
        ];

        echo "event: data\r\n";

        $ops = [
            "ops" => [
                [
                    "op" => "replace",
                    "path" => "",
                    "value" => [
                        "prompt_text" => $content,
                    ],
                ],
            ],
        ];
        echo 'data: ' . Json::encode($ops) . "\r\n";
        flush();

        try {
            $this->chatEventStream->send("pass_test", Yii::$app->params["gpt.api.completions.host"], Json::encode($fields));
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionPassTestIncorrectChat(Request $request): void
    {
        $prompt = $request->post("prompt");

        if ($prompt) {
            $content = $prompt;
        } else {
            $text = $request->post("content");
            $role = $request->post("role");
            $fragments = $request->post("fragments");

            $rolesMap = [
                "business_rx" => "Ты бизнес аналитик с большим опытом работы с системой электронного документооборота Directum RX, которой занимается созданием электронных тестов. Ты в совершенстве знаком с системой электронного документооборота Directum RX и её аналогах.",
                "systems_rx" => "Ты системный аналитик, спроектировавший множество решений для системы электронного документооборота Directum RX, которой занимается созданием электронных тестов. Ты в совершенстве знаком с системой электронного документооборота Directum RX и её аналогах.",
                "history_teacher" => "Ты учитель истории, которой занимается созданием электронных тестов для обучения.",
                "english_teacher" => "Ты школьный учитель английского языка, которой занимается созданием электронных тестов для обучения.",
                "biology_teacher" => "Ты школьный учитель биологии, которой занимается созданием электронных тестов для обучения.",
                "marketer" => "Ты маркетолог с большим опытом, которой занимается созданием электронных тестов для обучения.  Ты в совершенстве знаком с системой электронного документооборота Directum RX.",
            ];

            $roleText = $rolesMap[$role] ?? "";
            $fragmentsPrompt = "";
            if (!empty($fragments)) {
                $fragmentsPrompt = implode(", ", $fragments);
            }

            $content = <<<TEXT
$roleText
Есть следующий текст:
```
$text
```
По этому тексту сформирован список слов (словосочетаний):
```
$fragmentsPrompt
```
Для каждого слова (словосочетания) придумай по 3 неправильных, подходящих по смыслу ответа.
Если у тебя нет информации о каких-нибудь словах, то исключи их из списка.
Ответь в формате json: [{{"question": "слово из списка", "answers": ["неправильный ответ 1", "неправильный ответ 2"]}}]
TEXT;
        }

        $message = [
            "role" => "user",
            "content" => trim($content),
        ];

        $fields = [
            "input" => [
                "messages" => [
                    $message,
                ],
            ],
            "config" => [
                "metadata" => [
                    "conversation_id" => Uuid::uuid4()->toString(),
                ],
            ],
            "include_names" => [],
        ];

        echo "event: data\r\n";

        $ops = [
            "ops" => [
                [
                    "op" => "replace",
                    "path" => "",
                    "value" => [
                        "prompt_text" => $content,
                    ],
                ],
            ],
        ];
        echo 'data: ' . Json::encode($ops) . "\r\n";
        flush();

        try {
            $this->chatEventStream->send("pass_test_incorrect", Yii::$app->params["gpt.api.completions.host"], Json::encode($fields));
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionWikids(Request $request): void
    {
        $fields = $request->post();

        try {
            $this->chatEventStream->send("wikids_chat", Yii::$app->params["gpt.api.wikids.host"], Json::encode($fields));
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionStream(Request $request): void
    {
        $fields = $request->post();

        try {
            $this->chatEventStream->send("conversations", Yii::$app->params["gpt.api.completions.host"], Json::encode($fields));
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }
}
