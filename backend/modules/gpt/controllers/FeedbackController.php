<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use backend\JsonSchema\JsonSchemaValidator;
use common\rbac\UserRoles;
use Yii;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

class FeedbackController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_ADMIN],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $schema = <<<'JSON'
{
  "type": "object",
  "properties": {
    "score": {
      "type": "integer"
    },
    "run_id": {
      "type": "string"
    },
    "key": {
      "type": "string"
    },
    "value": {
      "type": "string"
    },
    "feedback_id": {
      "type": "string"
    },
    "comment": {
      "type": "string"
    },
    "source_info": {
      "type": "object",
      "properties": {
        "is_explicit": {
          "type": "boolean"
        }
      },
      "required": [
        "is_explicit"
      ]
    }
  },
  "required": [
    "score",
    "run_id",
    "key",
    "feedback_id"
  ]
}
JSON;

        $validator = new JsonSchemaValidator($schema);

        $json = Json::decode($request->rawBody, false);
        $validator->validate($json);

        if (!$validator->isValid()) {
            return ["success" => false, "message" => "Not valid"];
        }

        $exists = (new Query())->from("llm_feedback")->where(["run_id" => $json->run_id])->exists();
        if (!$exists) {
            return ["success" => false, "message" => "Not exists"];
        }

        try {
            $command = Yii::$app->db->createCommand();
            $command->update("llm_feedback", ["score" => $json->score], ["run_id" => $json->run_id]);
            $command->execute();
            return ["success" => true];
        } catch (\Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
            return ["success" => false, "message" => "Feedback error"];
        }
    }

    public function actionList(): string
    {
        $dataProvider = new SqlDataProvider([
            "sql" => '
                SELECT
                    target,
                    IF(input -> "$.input.question" IS NULL,
                      JSON_UNQUOTE(JSON_EXTRACT(input, CONCAT("$.input.messages[", JSON_LENGTH(input ->> "$.input.messages") -1 ,"].content"))),
                      JSON_UNQUOTE(input -> "$.input.question")
                    ) AS `input`,
                    CASE
                        WHEN output->"$.final_output" IS NULL
                        THEN JSON_UNQUOTE(output->"$.message")
                        ELSE JSON_UNQUOTE(output->"$.final_output")
                    END AS `output`,
                    created_at
                FROM llm_feedback
                ORDER BY created_at DESC',
            "totalCount" => (new Query())->from("llm_feedback")->count()
        ]);
        return $this->render("list", [
            "dataProvider" => $dataProvider,
        ]);
    }
}
