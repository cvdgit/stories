<?php

declare(strict_types=1);

namespace backend\LlmPrompt;

use Ramsey\Uuid\UuidInterface;
use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property string $name
 * @property string $key
 * @property string $prompt
 * @property int $created_at
 */
class LlmPrompt extends ActiveRecord
{
    public static function create(UuidInterface $id, string $name, string $key, string $prompt): self
    {
        $model = new self();
        $model->id = $id->toString();
        $model->name = $name;
        $model->key = $key;
        $model->prompt = $prompt;
        $model->created_at = time();
        return $model;
    }

    public function updatePrompt(string $name, string $key, string $prompt): void
    {
        $this->name = $name;
        $this->key = $key;
        $this->prompt = $prompt;
    }
}
