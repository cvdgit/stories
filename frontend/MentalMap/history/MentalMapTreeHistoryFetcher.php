<?php

declare(strict_types=1);

namespace frontend\MentalMap\history;

use yii\db\Query;

class MentalMapTreeHistoryFetcher
{
    public function fetch(string $mentalMapId, int $userId, array $treeData, bool $repetitionMode = false): array {
        $list = $this->flatten($treeData);
        if (!$repetitionMode) {
            $list = $this->createMentalMapTreeHistory($list, $mentalMapId, $userId);
        }
        return array_map(static function (array $item): array {
            return [
                'id' => $item['id'],
                'all' => $item['all'] ?? 0,
                'done' => $item['done'] ?? false,
            ];
        }, $list);
    }

    private function createMentalMapTreeHistory(array $nodeList, string $mentalMapId, int $userId): array
    {
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

        return array_map(static function (array $item) use ($rows): array {
            if (isset($rows[$item['id']])) {
                $item['done'] = (int) $rows[$item['id']]['all'] > 85;
                $item['all'] = (int) $rows[$item['id']]['all'];
                $item['hiding'] = (int) $rows[$item['id']]['hiding'];
                $item['target'] = (int) $rows[$item['id']]['target'];
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
