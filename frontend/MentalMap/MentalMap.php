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

    public static function isDone(array $history): bool
    {
        if (count($history) === 0) {
            return false;
        }
        return array_reduce($history, static function (bool $carry, array $item): bool {
            return $carry && (int) $item['all'] >= 80;
        }, true);
    }

    public static function calcHistoryPercent(array $history): int
    {
        if (count($history) === 0) {
            return 100;
        }
        $doneItems = array_filter($history, static function(array $item): bool {
            return $item['done'] || ($item['all'] ?? 0) >= 80;
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
