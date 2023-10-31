<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use yii\db\Query;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class ChatController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionGetData(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $query = (new Query())->select("payload")
            ->from("conversation")
            ->where([
                "user_id" => $user->getId()
            ])
            ->orderBy(["created_at" => SORT_ASC]);



        $conversations = [];
        foreach ($query->all() as $conversationPayload) {
            $conversation = Json::decode($conversationPayload["payload"]);

            $messagesQuery = (new Query())
                ->select("payload")
                ->from("conversation_message")
                ->where([
                    "conversation_uuid" => $conversation["id"],
                ])
                ->orderBy(["created_at" => SORT_ASC]);

            $conversation["messages"] = array_map(static function(array $message): array {
                $payload = Json::decode($message["payload"]);
                if (isset($payload["conversation_id"])) {
                    unset($payload["conversation_id"]);
                }
                return $payload;
            }, $messagesQuery->all());

            $conversations[] = $conversation;
        }

        return [
            "conversation" => [],
            "current" => 0,
            "chat" => $conversations,
            "currentChat" => 0,
            "options" => [
                "account" => [
                    "name" => "CHAT——AI",
                    "avatar" => "",
                ],
                "general" => [
                    "language" => "English",
                    "theme" => "light",
                    "command" => "COMMAND_ENTER",
                    "size" => "normal",
                ],
                "openai" => [
                    "baseUrl" => "",
                    "organizationId" => "",
                    "temperature" => 1,
                    "model" => "gpt-3.5-turbo",
                    "apiKey" => \Yii::$app->params["gpt.key"],
                    "max_tokens" => 2048,
                    "n" => 1,
                    "stream" => true,
                ],
            ],
            "is" => [
                "typing" => false,
                "config" => false,
                "fullScreen" => true,
                "sidebar" => true,
                "inputting" => false,
                "thinking" => false,
                "apps" => true,
            ],
            "typingMessage" => [],
            "content" => "",
        ];
    }

    public function actionConversations(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $action = $request->post("action");
        $payload = $request->post('payload');

        switch ($action) {
            case "new":
                \Yii::$app->db->createCommand()
                    ->insert("conversation", [
                        "uuid" => $payload["id"],
                        "title" => $payload["title"],
                        "payload" => $payload,
                        "created_at" => time(),
                        "user_id" => $user->getId(),
                    ])
                    ->execute();
                break;
            case "modify":
                \Yii::$app->db->createCommand()
                    ->update("conversation", [
                        "title" => $payload["title"],
                        "payload" => $payload,
                    ], [
                        "user_id" => $user->getId(),
                        "uuid" => $payload["id"],
                    ])
                    ->execute();
                break;
            case "remove":
                \Yii::$app->db->createCommand()
                    ->delete("conversation", [
                        "user_id" => $user->getId(),
                        "uuid" => $payload["id"],
                    ])
                    ->execute();
                \Yii::$app->db->createCommand()
                    ->delete("conversation_message", [
                        "conversation_uuid" => $payload["id"],
                    ])
                    ->execute();
                break;
        }

        return ["success" => true];
    }

    public function actionMessages(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $action = $request->post("action");
        $payload = $request->post('payload');

        switch ($action) {
            case "new":
                \Yii::$app->db->createCommand()
                    ->insert("conversation_message", [
                        "uuid" => $payload["id"],
                        "conversation_uuid" => $payload["conversation_id"],
                        "payload" => $payload,
                        "created_at" => time(),
                    ])
                    ->execute();
                break;
            case "modify":
                break;
            case "remove":
                $isUserMessage = (new Query())
                    ->from(["c" => "conversation"])
                    ->where([
                        "c.uuid" => $payload["conversation_id"],
                        "c.user_id" => $user->getId()
                    ])
                    ->innerJoin(["cm" => "conversation_message", "c.uuid = cm.conversation_uuid"])
                    ->andWhere(["cm.uuid" => $payload["id"]])
                    ->exists();
                if ($isUserMessage) {
                    \Yii::$app->db->createCommand()
                        ->delete("conversation_message", [
                            "uuid" => $payload["id"],
                        ])
                        ->execute();
                }
                break;
        }

        return ["success" => true];
    }
}
