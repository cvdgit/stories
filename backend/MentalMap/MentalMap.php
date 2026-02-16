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
 * @property string $map_type
 * @property string|null $source_mental_map_id
 */
class MentalMap extends ActiveRecord
{
    public const TYPE_MENTAL_MAP = 'mental-map';
    public const TYPE_MENTAL_MAP_EVEN_FRAGMENTS = 'mental-map-even-fragments';
    public const TYPE_MENTAL_MAP_ODD_FRAGMENTS = 'mental-map-odd-fragments';
    public const TYPE_MENTAL_MAP_PLAN = 'mental-map-plan';
    public const TYPE_MENTAL_MAP_PLAN_ACCUMULATION = 'mental-map-plan-accumulation';
    public const TYPE_MENTAL_MAP_QUESTIONS = 'mental-map-questions';

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function create(
        string $uuid,
        string $name,
        array $payload,
        int $userId,
        string $type = self::TYPE_MENTAL_MAP
    ): self {
        $model = new self();
        $model->uuid = $uuid;
        $model->name = $name;
        $model->payload = $payload;
        $model->user_id = $userId;
        $model->map_type = $type;
        return $model;
    }

    public static function createFromPayload(string $id, MentalMapPayload $payload, int $userId, string $type): self
    {
        return self::create(
            $id,
            $payload->getName(),
            $payload->asArray(),
            $userId,
            $type
        );
    }

    public static function createMentalMapQuestions(
        string $uuid,
        string $name,
        array $payload,
        int $userId,
        string $sourceUuid
    ): self {
        $model = self::create($uuid, $name, $payload, $userId);
        $model->map_type = self::TYPE_MENTAL_MAP_QUESTIONS;
        $model->source_mental_map_id = $sourceUuid;
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

    public function isMentalMapAsTree(): bool
    {
        return $this->payload['treeView'] ?? false;
    }

    public function getTreeData(): array
    {
        return $this->payload['treeData'] ?? [];
    }

    private function flatten(array $element): array
    {
        $flatArray = [];
        foreach ($element as $key => $node) {
            if (array_key_exists('children', $node)) {
                $flatArray = array_merge($flatArray, $this->flatten($node['children'] ?? []));
                unset($node['children']);
            }
            $flatArray[] = $node;
        }
        return $flatArray;
    }

    public function getItems(): array
    {
        $items = $this->getImages();
        if ($this->isMentalMapAsTree()) {
            return $this->flatten($this->getTreeData());
        }
        return $items;
    }

    public function updateMapText(string $text): void
    {
        $payload = $this->payload;
        $payload['text'] = $text;
        $this->payload = $payload;
    }

    public function updateMapTitle(string $title): void
    {
        $payload = $this->payload;
        $payload['name'] = $title;
        $this->payload = $payload;
        $this->name = $title;
    }

    public function updateSettings(array $settings): void
    {
        $payload = $this->payload;
        $payload['settings'] = $settings;
        $this->payload = $payload;
        $this->schedule_id = $settings['scheduleId'] ?? null;
    }

    public function getSettings(): array
    {
        return $this->payload['settings'] ?? [];
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

    public function getQuestions(): array
    {
        return $this->payload['questions'] ?? [];
    }

    public function updateQuestions(array $fragments): void
    {
        $payload = $this->payload;
        $payload['questions'] = $fragments;
        $this->payload = $payload;
    }

    public function typeIsQuestions(): bool
    {
        return $this->map_type === self::TYPE_MENTAL_MAP_QUESTIONS;
    }
}
