<?php

declare(strict_types=1);

namespace backend\modules\gpt;

use gamringer\JSONPatch\Patch;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Exception;
use yii\helpers\Json;

class ChatEventStream
{
    /**
     * @var EventStream
     */
    private $eventStream;

    public function __construct()
    {
        $this->eventStream = new EventStream();
    }

    /**
     * @throws Exception
     */
    public function send(string $target, string $url, string $fieldsJson): void
    {
        $streamedResponse = (object) ["id" => ""];
        $errorResponse = [];

        try {
            $this->eventStream->send(
                $url,
                $fieldsJson,
                static function (string $chunk) use (&$streamedResponse, &$errorResponse): void {
                    foreach (explode("\r\n\r\n", $chunk) as $row) {
                        if (!$row) {
                            continue;
                        }
                        $rows = explode("\n", $row);
                        $event = explode(" ", $rows[0])[1];
                        if (trim($event) === "data") {
                            $data = str_replace("data: ", "", $rows[1]);
                            try {
                                $dataJson = Json::decode($data);
                            } catch (\Exception $ex) {
                                $data = preg_replace('/[[:cntrl:]]/', '', $data);
                                $dataJson = Json::decode($data);
                            }
                            $op = Patch::fromJSON(Json::encode($dataJson["ops"]));
                            $op->apply($streamedResponse);
                        }
                        if (trim($event) === "error") {
                            $data = str_replace("data: ", "", $rows[1]);
                            $errorResponse = Json::decode($data);
                        }
                    }
                }
            );
        } catch (\Exception $ex) {
            Yii::$app->errorHandler->logException($ex);

            echo "event: error\n";
            $ops = [
                "ops" => [
                    [
                        "op" => "replace",
                        "path" => "",
                        "value" => [
                            "error_text" => "При обработке запроса произошла ошибка.",
                        ],
                    ],
                ],
            ];
            echo 'data: ' . Json::encode($ops) . "\n\n";
            flush();
        }

        $command = Yii::$app->db->createCommand();
        $runId = $streamedResponse->id;
        if (empty($runId)) {
            $runId = Uuid::uuid4()->toString();
        }
        $command->insert("llm_feedback", [
            "run_id" => $runId,
            "target" => $target,
            "user_id" => Yii::$app->user->getId(),
            "input" => Json::decode($fieldsJson),
            "output" => isset($errorResponse["message"]) ? $errorResponse : $streamedResponse,
            "created_at" => time(),
        ]);
        $command->execute();
    }
}
