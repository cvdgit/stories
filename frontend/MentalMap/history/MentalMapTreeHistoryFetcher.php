<?php

declare(strict_types=1);

namespace frontend\MentalMap\history;

use frontend\MentalMap\MentalMap;
use yii\db\Query;

class MentalMapTreeHistoryFetcher
{
    public function fetch(
        string $mentalMapId,
        int $userId,
        array $treeData,
        int $threshold,
        bool $repetitionMode = false
    ): array {
        $list = $this->flatten($treeData);
        if (!$repetitionMode) {
            $list = $this->createMentalMapTreeHistory($list, $mentalMapId, $userId, $threshold);
        }
        return array_map(static function (array $item): array {
            return [
                'id' => $item['id'],
                'all' => $item['all'] ?? 0,
                'done' => $item['done'] ?? false,
            ];
        }, $list);
    }

    private function createMentalMapTreeHistory(
        array $nodeList,
        string $mentalMapId,
        int $userId,
        int $threshold
    ): array {
        $history = array_map(static function (array $node): array {
            return [
                'id' => $node['id'],
                'done' => false,
            ];
        }, $nodeList);

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

        return array_map(static function (array $item) use ($rows, $threshold): array {
            if (isset($rows[$item['id']])) {
                $row = $rows[$item['id']];
                $all = isset($row['all']) ? (int) $row['all'] : 0;
                $item['done'] = MentalMap::fragmentIsDone($all, $threshold);
                $item['all'] = $all;
                $item['hiding'] = isset($row['hiding']) ? (int) $row['hiding'] : 0;
                $item['target'] = isset($row['target']) ? (int) $row['target'] : 0;
            }
            return $item;
        }, $history);
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
}
