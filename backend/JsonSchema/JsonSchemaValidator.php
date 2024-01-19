<?php

declare(strict_types=1);

namespace backend\JsonSchema;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;
use yii\helpers\Json;

class JsonSchemaValidator
{
    private $validator;
    private $jsonSchemaObject;

    public function __construct(string $schema)
    {
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
