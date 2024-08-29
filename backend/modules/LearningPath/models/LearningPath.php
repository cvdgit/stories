<?php

declare(strict_types=1);

namespace backend\modules\LearningPath\models;

use Ramsey\Uuid\UuidInterface;
use yii\db\ActiveRecord;

/**
 * @property string $uuid [varchar(36)]
 * @property string $name [varchar(255)]
 * @property array $payload [json]
 * @property int $user_id [int(11)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 */
class LearningPath extends ActiveRecord
{
    public static function create(UuidInterface $uuid, string $name, array $payload, int $userId): LearningPath
    {
        $model = new self();
        $model->uuid = $uuid->toString();
        $model->name = $name;
        $model->payload = $payload;
        $model->user_id = $userId;
        $model->created_at = time();
        $model->updated_at = time();
        return $model;
    }

    public function updatePayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function updateTreeName(string $tree, string $name): void
    {
        $payload = $this->payload;
        $payload[$tree]['name'] = $name;
        $this->payload = $payload;
    }

    public function deleteTree(string $tree): void
    {
        $payload = $this->payload;
        unset($payload[$tree]);
        $this->payload = $payload;
    }

    public function createTree(string $tree, string $name): void
    {
        $payload = $this->payload;
        $payload[$tree] = [
            'name' => $name,
            'items' => [],
        ];
        $this->payload = $payload;
    }
}
