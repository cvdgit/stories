<?php

declare(strict_types=1);

namespace frontend\MentalMap\history;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use frontend\MentalMap\MentalMap;
use yii\db\Expression;
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
                'allPrev' => $item['allPrev'],
                'hiding' => isset($item['hiding']) ? (int) $item['hiding'] : 0,
                'target' => isset($item['target']) ? (int) $item['target'] : 0,
                'done' => $item['done'] ?? false,
                'seconds' => isset($item['seconds']) ? (int) $item['seconds'] : 0,
                'hidingPrev' => $item['hidingPrev'],
            ];
        }, $list);
    }

    /**
     * @throws Exception
     */
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
                'all' => 0,
                'allPrev' => 0,
                'hiding' => 0,
                'hidingPrev' => 0,
                'target' => 0,
                'seconds' => 0,
            ];
        }, $nodeList);

        /*$oldRows = (new Query())
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
            ->all();*/

        $today = (new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow')))
                ->setTime(0, 0)
                ->format('U') . ' + (3 * 60 * 60)';
        $hidingBeforeQuery = (new Query())
            ->select(new Expression('MAX(t.text_hiding_percentage)'))
            ->from(['t' => 'mental_map_history'])
            ->where('t.image_fragment_id = h.image_fragment_id')
            ->andWhere('t.overall_similarity >= IFNULL(t.threshold, 0)')
            ->andWhere(['<=', new Expression('t.created_at'), new Expression($today)]);

        $allBeforeQuery = (new Query())
            ->select(new Expression('MAX(t.overall_similarity)'))
            ->from(['t' => 'mental_map_history'])
            ->where('t.image_fragment_id = h.image_fragment_id')
            ->andWhere(['<=', new Expression('t.created_at'), new Expression($today)]);

        $historyRows = (new Query())
            ->select([
                'id' => 'h.image_fragment_id',
                'all' => 'h.overall_similarity',
                'allPrev' => $allBeforeQuery,
                'hiding' => 'h.text_hiding_percentage',
                'hidingPrev' => $hidingBeforeQuery,
                'target' => 'h.text_target_percentage',
                'all_words' => 'h.all_important_words_included',
                'seconds' => 'h.seconds',
            ])
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.mental_map_id' => $mentalMapId,
                'h.user_id' => $userId,
            ])
            ->andWhere("h.overall_similarity >= IFNULL(h.threshold, $threshold)")
            ->orderBy(['all' => SORT_DESC])
            ->all();

        $historyRowsByFragmentId = [];
        foreach ($historyRows as $historyRow) {
            $id = $historyRow['id'];
            if (!isset($historyRowsByFragmentId[$id])) {
                $historyRowsByFragmentId[$id] = [];
            }
            $historyRowsByFragmentId[$id][] = $historyRow;
        }

        $rows = $this->filterUserHistoryGroupByCorrect($historyRowsByFragmentId);

        return array_map(static function (array $item) use ($rows): array {
            if (isset($rows[$item['id']])) {
                $row = $rows[$item['id']];
                $all = isset($row['all']) ? (int) $row['all'] : 0;
                $item['done'] = $row['done'];
                $item['all'] = $all;
                $item['allPrev'] = (int) ($row['allPrev'] ?? 0);
                $item['hidingPrev'] = (int) $rows[$item['id']]['hidingPrev'];
                $item['seconds'] = (int) $rows[$item['id']]['seconds'];
                $item['hiding'] = isset($row['hiding']) ? (int) $row['hiding'] : 0;
                $item['target'] = isset($row['target']) ? (int) $row['target'] : 0;
            }
            return $item;
        }, $history);
    }

    public function filterUserHistoryGroupByCorrect(array $historyRowsByFragmentId): array
    {
        $rows = [];
        foreach ($historyRowsByFragmentId as $fragmentId => $rowsGroup) {

            $allWordsDoneItems = array_values(array_filter($rowsGroup, static function(array $groupItem): bool {
                return $groupItem['all_words'] === '1';
            }));
            if (count($allWordsDoneItems) > 0) {
                $rows[$fragmentId] = array_merge($allWordsDoneItems[0], ['done' => true]);
                continue;
            }

            $allWordsFailItems = array_filter($rowsGroup, static function(array $groupItem): bool {
                return $groupItem['all_words'] === '-1';
            });
            if (count($allWordsFailItems) > 0) {
                $rows[$fragmentId] = array_merge($allWordsFailItems[0], ['done' => false]);
                continue;
            }

            $rows[$fragmentId] = array_merge($rowsGroup[0], ['done' => true]);
        }
        return $rows;
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
