<?php

declare(strict_types=1);

namespace frontend\MentalMap;

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
    public function getImages(): array
    {
        if (!isset($this->payload['map'])) {
            return [];
        }
        return $this->payload['map']['images'] ?? [];
    }

    public static function isDone(array $history, int $threshold): bool
    {
        if (count($history) === 0) {
            return false;
        }
        return array_reduce($history, static function (bool $carry, array $item) use ($threshold): bool {
            return $carry && self::fragmentIsDone((int) $item['all'], $threshold);
        }, true);
    }

    public static function fragmentIsDone(int $value, int $threshold): bool
    {
        return $value >= $threshold;
    }

    public static function calcHistoryPercent(array $history, int $threshold): int
    {
        if (count($history) === 0) {
            return 100;
        }
        $doneItems = array_filter($history, static function(array $item) use ($threshold): bool {
            $all = isset($item['all']) ? (int) $item['all'] : 0;
            return $item['done'] || self::fragmentIsDone($all, $threshold);
        });
        if (count($doneItems) === 0) {
            return 0;
        }
        return (int) round(count($doneItems) * 100 / count($history));
    }

    public function isMentalMapAsTree(): bool
    {
        return $this->payload['treeView'] ?? false;
    }

    public function getTreeData(): array
    {
        return $this->payload['treeData'] ?? [];
    }

    public function getMapData(): array
    {
        return $this->payload['map'] ?? [];
    }

    public function findImageFromPayload(string $imageId): ?array
    {
        $items = array_values(array_filter($this->getItems(), static function (array $item) use ($imageId): bool {
            return $item['id'] === $imageId;
        }));
        if (count($items) === 0) {
            return null;
        }
        return $items[0];
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

    public function mapTypeIsMentalMapQuestions(): bool
    {
        return $this->map_type === 'mental-map-questions';
    }

    public function getSettingsData(): array
    {
        return $this->payload['settings'] ?? [];
    }
}
