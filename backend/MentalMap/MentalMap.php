<?php

declare(strict_types=1);

namespace backend\MentalMap;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property string $uuid [varchar(36)]
 * @property string $name [varchar(255)]
 * @property array $payload [json]
 * @property int $user_id [int(11)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 * @property int|null $schedule_id
 */
class MentalMap extends ActiveRecord
{
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function create(string $uuid, string $name, array $payload, int $userId): MentalMap
    {
        $model = new self();
        $model->uuid = $uuid;
        $model->name = $name;
        $model->payload = $payload;
        $model->user_id = $userId;
        return $model;
    }

    public function updateMap(string $url, int $width, int $height, array $images): void
    {
        $payload = $this->payload;
        $payload['map']['url'] = $url;
        $payload['map']['width'] = $width;
        $payload['map']['height'] = $height;
        $payload['map']['images'] = $images;
        $this->payload = $payload;
    }

    public function getImages(): array
    {
        if (!isset($this->payload['map'])) {
            return [];
        }
        return $this->payload['map']['images'] ?? [];
    }

    public function updateMapText(string $text): void
    {
        $payload = $this->payload;
        $payload['text'] = $text;
        $this->payload = $payload;
    }

    public function updateSettings(array $settings): void
    {
        $payload = $this->payload;
        $payload['settings'] = $settings;
        $this->payload = $payload;
        $this->schedule_id = $settings['scheduleId'] ?? null;
    }

    public function findImageFromPayload(string $imageId): ?array
    {
        return array_values(array_filter($this->getImages(), static function (array $item) use ($imageId): bool {
            return $item['id'] === $imageId;
        }))[0];
    }

    public function updateTreeData(array $treeData): void
    {
        $payload = $this->payload;
        $payload['treeData'] = $treeData;
        $this->payload = $payload;
    }
}
