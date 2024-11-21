<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use backend\modules\gpt\ChatEventStream;
use Exception;
use frontend\GptChat\GptChatForm;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Query;
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
        if ($action->id === "stream" || $action->id === "pdf") {
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
        echo 'data: ' . Json::encode($ops) . "\r\n\r\n";
        flush();

        try {
            $this->chatEventStream->send(
                "pass_test",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode($fields),
            );
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
            $lang = $request->post("lang");

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

            $langText = "";
            if ($lang) {
                $langText = "на английском языке";
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
                Для каждого слова (словосочетания) придумай по 3 неправильных, подходящих по смыслу ответа $langText.
                Если у тебя нет информации о каких-нибудь словах, то исключи их из списка.
                Ответь в формате json: [{{"question": "слово из списка", "answers": ["неправильный ответ 1", "неправильный ответ 2"], "translate": "слово на английском"}}]
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
        echo 'data: ' . Json::encode($ops) . "\r\n\r\n";
        flush();

        try {
            $this->chatEventStream->send(
                "pass_test_incorrect",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode($fields),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionWikids(Request $request): void
    {
        $chatForm = new GptChatForm();
        if ($chatForm->load($request->post())) {
            if (!$chatForm->validate()) {
                echo "event: error\r\n";
                $ops = [
                    "ops" => [
                        [
                            "op" => "replace",
                            "path" => "",
                            "value" => [
                                "error_text" => "Not valid.",
                            ],
                        ],
                    ],
                ];
                echo 'data: ' . Json::encode($ops) . "\r\n\r\n";
                flush();
                return;
            }

            $fields = [
                "input" => [
                    "question" => $chatForm->text,
                    "chat_history" => [],
                ],
                "config" => [
                    "metadata" => [
                        "conversation_id" => Uuid::uuid4()->toString(),
                    ],
                ],
                "include_names" => ["FindDocs"],
            ];

            try {
                $this->chatEventStream->send(
                    "wikids_chat",
                    Yii::$app->params["gpt.api.wikids.host"],
                    Json::encode($fields),
                );
            } catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
            }
        } else {
            echo "event: error\r\n";
            $ops = [
                "ops" => [
                    [
                        "op" => "replace",
                        "path" => "",
                        "value" => [
                            "error_text" => "No data.",
                        ],
                    ],
                ],
            ];
            echo 'data: ' . Json::encode($ops) . "\r\n\r\n";
            flush();
        }
    }

    public function actionStream(Request $request): void
    {
        $fields = $request->post();

        try {
            $this->chatEventStream->send(
                "conversations",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode($fields),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionPdf(Request $request): void
    {
        $fields = $request->post();

        try {
            $this->chatEventStream->send("pdf", Yii::$app->params["gpt.api.pdf.host"], Json::encode($fields));
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionRetelling(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $userResponse = $payload["userResponse"];
        $slideTexts = $payload["slideTexts"];

        $content = <<<TEXT
            Исходный текст:
            ```
            $slideTexts
            ```
            Пересказ:
            ```
            $userResponse
            ```
            Изложи исходный текст полностью  по предложениям.
            Укажи степень сходства каждого сведения исходного текста с пересказом в процентах.
            Укажи общую степень сходства сведений исходного текста и пересказа в процентах.
            Ответь в формате json.
            В ответе не используй символы unicode.
            Пример: {"sentences_similarity": [{"original": "Оригинальное предложение", "rewrite": "Предложение, пересказанное пользователем", "similarity": "Процент сходства, целое число"}], "overall_similarity": "Итоговый процент сходства, целое число"}
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
            $this->chatEventStream->send(
                "retelling",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode($fields),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionRetellingAnswers(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $slideTexts = $payload["slideTexts"];

        $content = <<<TEXT
            Текст:
            ```
            $slideTexts
            ```
            Задай вопрос к каждому предложению и расположи его новой строки.
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
            $this->chatEventStream->send(
                "retelling-answers",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode($fields),
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

    public function actionRewriteText(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $slideTexts = $payload['content'];
        $promptId = $payload['promptId'];
        $editedPrompt = $payload['prompt'] ?? null;

        if ($editedPrompt !== null) {
            $prompt = $editedPrompt;
        } else {
            $prompt = (new Query())
                ->select('t.prompt')
                ->from(['t' => 'llm_prompt'])
                ->where(['t.id' => $promptId])
                ->scalar();

            if (!$prompt) {
                $this->flushError('Промт не найден');
                Yii::$app->end();
            }
        }

        $content = $prompt;

        $message = [
            "role" => "user",
            "content" => trim(str_replace('{TEXT}', $slideTexts, $content)),
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

        /*echo "event: data\r\n";

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
        echo 'data: ' . Json::encode($ops) . "\r\n\r\n";
        flush();*/

        try {
            $this->chatEventStream->send(
                "rewrite",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode($fields),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionRetellingRewrite(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $userResponse = $payload["userResponse"];
        $slideTexts = $payload["slideTexts"];

        $content = (new Query())
            ->select('t.prompt')
            ->from(['t' => 'llm_prompt'])
            ->where(['t.key' => 'text-rewrite'])
            ->scalar();

        if (!$content) {
            $this->flushError('Промт не найден');
            Yii::$app->end();
        }

        $message = [
            "role" => "user",
            "content" => trim(str_replace(['{TEXT}', '{USER_RESPONSE}'], [$slideTexts, $userResponse], $content)),
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
            $this->chatEventStream->send(
                "retelling",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode($fields),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }

    public function actionQuestion(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $userResponse = $payload['userResponse'];
        $promptId = $payload['promptId'] ?? null;
        $job = $payload['job'] ?? null;
        $questionId = $payload['questionId'] ?? null;

        if ($questionId !== null) {
            $questionPayload = (new Query())
                ->select('regions')
                ->from('story_test_question')
                ->where(['id' => $questionId])
                ->scalar();
            if (!$questionPayload) {
                $this->flushError('Вопрос не найден');
                Yii::$app->end();
            }
            $questionPayload = Json::decode($questionPayload);
            $job = $questionPayload['job'];
            $promptId = $questionPayload['promptId'];
        }

        if (!$promptId) {
            $this->flushError('Промт не определен');
            Yii::$app->end();
        }

        $content = (new Query())
            ->select('t.prompt')
            ->from(['t' => 'llm_prompt'])
            ->where(['t.id' => $promptId])
            ->scalar();

        if (!$content) {
            $this->flushError('Промт не найден');
            Yii::$app->end();
        }

        if (!$content && !$userResponse) {
            $this->flushError('Нет обязательных параметров');
            Yii::$app->end();
        }

        if (strpos($content, '{JOB}') === false) {
            $this->flushError('В промте нет шаблона {JOB}');
            Yii::$app->end();
        }

        if (strpos($content, '{USER_RESPONSE}') === false) {
            $this->flushError('В промте нет шаблона {USER_RESPONSE}');
            Yii::$app->end();
        }

        $message = [
            "role" => "user",
            "content" => trim(str_replace(['{JOB}', '{USER_RESPONSE}'], [$job, $userResponse], $content)),
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
            $this->chatEventStream->send(
                "question",
                Yii::$app->params["gpt.api.completions.host"],
                Json::encode($fields),
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }
}
