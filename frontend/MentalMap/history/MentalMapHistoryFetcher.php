<?php

declare(strict_types=1);

namespace frontend\MentalMap\history;

use yii\db\Query;

class MentalMapHistoryFetcher
{
    public function fetch(array $fragments, string $mentalMapId, int $userId, bool $repetitionMode = false): array
    {
        if (!$repetitionMode) {
            $items = $this->createMentalMapHistory($fragments, $mentalMapId, $userId);
        }
        return array_map(static function (array $item): array {
            return [
                'id' => $item['id'],
                'all' => $item['all'] ?? 0,
                'hiding' => $item['hiding'] ?? 0,
                'target' => $item['target'] ?? 0,
                'done' => ($item['all'] ?? 0) > 75,
            ];
        }, $items ?? $fragments);
    }

    private function createMentalMapHistory(array $images, string $mentalMapId, int $userId): array
    {
        $history = array_map(static function (array $image): array {
            return [
                'id' => $image['id'],
                'all' => 0,
                'hiding' => 0,
                'target' => 0,
            ];
        }, $images);

        $rows = (new Query())
            ->select([
                'id' => 'h.image_fragment_id',
                'all' => 'MAX(h.overall_similarity)',
                'hiding' => 'MAX(h.text_hiding_percentage)',
                'target' => 'MAX(h.text_target_percentage)',
            ])
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.mental_map_id' => $mentalMapId,
                'h.user_id' => $userId,
            ])
            ->groupBy('h.image_fragment_id')
            ->indexBy('id')
            ->all();

        return array_map(static function (array $item) use ($rows): array {
            if (isset($rows[$item['id']])) {
                $item['all'] = (int) $rows[$item['id']]['all'];
                $item['hiding'] = (int) $rows[$item['id']]['hiding'];
                $item['target'] = (int) $rows[$item['id']]['target'];
            }
            return $item;
        }, $history);
    }
}
