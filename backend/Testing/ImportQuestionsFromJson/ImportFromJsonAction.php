<?php

declare(strict_types=1);

namespace backend\Testing\ImportQuestionsFromJson;

use backend\services\ImportQuestionService;
use common\models\StoryTest;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use yii\base\Action;
use yii\helpers\Json;
use yii\web\Request;
use yii\web\Response;

class ImportFromJsonAction extends Action
{
    /**
     * @var ImportQuestionService
     */
    private $importQuestionService;

    public function __construct($id, $controller, ImportQuestionService $importQuestionService, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->importQuestionService = $importQuestionService;
    }

    /**
     * @throws \JsonException
     */
    public function run(int $test_id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $testModel = StoryTest::findOne($test_id);
        if ($testModel === null) {
            return ["success" => false, "message" => "Тест не найден"];
        }

        $jsonBody = Json::decode($request->rawBody, false);
        $content = $jsonBody->content;

        $schema = <<<'JSON'
{
  "type": "array",
  "items": [
    {
      "type": "object",
      "properties": {
        "question": {
          "type": "string"
        },
        "answers": {
          "type": "array",
          "items": [
            {
              "type": "object",
              "properties": {
                "answer": {
                  "type": "string"
                },
                "correct": {
                  "type": ["string", "boolean"]
                }
              },
              "required": [
                "answer",
                "correct"
              ]
            }
          ]
        }
      },
      "required": [
        "question",
        "answers"
      ]
    }
  ]
}
JSON;

        $jsonSchemaObject = Json::decode($schema);
        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema('file://mySchema', $jsonSchemaObject);

        $validator = new Validator(new Factory($schemaStorage));
        $validator->validate($content, $jsonSchemaObject);

        if ($validator->isValid()) {
            try {
                $this->importQuestionService->createFromJson($test_id, $content);
                return ["success" => true];
            } catch (\Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                return ["success" => false, "message" => $exception->getMessage()];
            }
        } else {
            return ["success" => false, "message" => "JSON not valid"];
        }
    }
}
