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
}
