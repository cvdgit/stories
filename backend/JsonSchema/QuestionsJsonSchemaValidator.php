<?php

declare(strict_types=1);

namespace backend\JsonSchema;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use yii\helpers\Json;

class QuestionsJsonSchemaValidator
{
    private $validator;
    private $jsonSchemaObject;
    public function __construct()
    {
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
                  "type": "boolean"
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
        $this->jsonSchemaObject = Json::decode($schema);
        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema('file://mySchema', $this->jsonSchemaObject);

        $this->validator = new Validator(new Factory($schemaStorage));
    }

    public function validate($value): int
    {
        return $this->validator->validate($value, $this->jsonSchemaObject);
    }

    public function isValid(): bool
    {
        return $this->validator->isValid();
    }
}
