<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

class StreamController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionChat(Request $request, Response $response)
    {
        $response->format = Response::FORMAT_RAW;
        $response->stream = true;
        $response->isSent = true;
        \Yii::$app->session->close();

        @ob_end_clean();
        ini_set('output_buffering', '0');
        //set_time_limit(0);

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache, must-revalidate");
        header("X-Accel-Buffering: no");
        header("Connection: keep-alive");

        $fields = [
            "content" => $request->post("content"),
            "questions" => $request->post("questions"),
            "answers" => $request->post("answers"),
            "role" => $request->post("role"),
        ];

        $options = [
            CURLOPT_URL => \Yii::$app->params["gpt.api.host"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => Json::encode($fields),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
            CURLOPT_WRITEFUNCTION => function($ch, $chunk) {
                echo $chunk;
                //sleep(1);
                flush();
                return strlen($chunk);
            },
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        curl_exec($ch);

        $error = curl_error($ch);
        if ($error !== "") {
            echo $error;
        }

        curl_close($ch);

        //$response->statusCode = 404;
        //$response->data = 'no';
    }

    public function actionPassTestChat(Request $request, Response $response)
    {
        $response->format = Response::FORMAT_RAW;
        $response->stream = true;
        $response->isSent = true;
        \Yii::$app->session->close();

        @ob_end_clean();
        ini_set('output_buffering', '0');
        set_time_limit(0);

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache, must-revalidate");
        header("X-Accel-Buffering: no");
        header("Connection: keep-alive");

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
            $fragmentsText = implode(", ", array_map(static function(string $f) {
                return '"' . $f . '"';
            }, $fragments));
            $fragmentsPrompt = <<<TXT
            Исключи слова: $fragmentsText
TXT;
        }

        $content = <<<TEXT
            $roleText
            Проанализируй текст: $text.
            Выбери из текста слова, которые, на твой взгляд, определяют его суть.
            Эти слова не должны идти друг за другом в тексте если это не словосочетание.
            Не меняй формы слов. Слова оставляй также, как они написаны в тексте.
            Слова должны быть написаны также, как они встречаются в тексте.
            $fragmentsPrompt
            Ответь в формате json: ["слово"]
TEXT
;

        $message = [
            "role" => "user",
            "content" => trim($content),
        ];

        $fields = [
            "messages" => [
                $message,
            ],
        ];

        $options = [
            CURLOPT_URL => \Yii::$app->params["gpt.api.completions.host"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => Json::encode($fields),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
            CURLOPT_WRITEFUNCTION => function($ch, $chunk) {
                echo $chunk;
                //sleep(1);
                flush();
                return strlen($chunk);
            },
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        curl_exec($ch);

        $error = curl_error($ch);
        if ($error !== "") {
            echo $error;
        }

        curl_close($ch);

        //$response->statusCode = 404;
        //$response->data = 'no';
    }

    public function actionPassTestIncorrectChat(Request $request, Response $response)
    {
        $response->format = Response::FORMAT_RAW;
        $response->stream = true;
        $response->isSent = true;
        \Yii::$app->session->close();

        @ob_end_clean();
        ini_set('output_buffering', '0');
        set_time_limit(0);

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache, must-revalidate");
        header("X-Accel-Buffering: no");
        header("Connection: keep-alive");

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
            Проанализируй текст: $text.
            Вот список слов (словосочетаний) из этого текст:
            ===
            $fragmentsPrompt
            ===
            Проанализируй эти слова (словосочетания) и для каждого придумай по 3 неправильных, подходящих по смыслу ответа.
            Если у тебя нет информации о каких-нибудь словах, то исключи их из списка.
            Ответь в формате json: [{{"question": "слово из списка", "answers": ["неправильный ответ 1", "неправильный ответ 2"]}}]
TEXT
        ;

        $message = [
            "role" => "user",
            "content" => trim($content),
        ];

        $fields = [
            "messages" => [
                $message,
            ],
        ];

        $options = [
            CURLOPT_URL => \Yii::$app->params["gpt.api.completions.host"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => Json::encode($fields),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
            CURLOPT_WRITEFUNCTION => function($ch, $chunk) {
                echo $chunk;
                //sleep(1);
                flush();
                return strlen($chunk);
            },
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        curl_exec($ch);

        $error = curl_error($ch);
        if ($error !== "") {
            echo $error;
        }

        curl_close($ch);

        //$response->statusCode = 404;
        //$response->data = 'no';
    }
}
