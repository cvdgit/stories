<?php

declare(strict_types=1);

namespace frontend\MentalMap\history;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use frontend\MentalMap\MentalMap;
use yii\db\Expression;
use yii\db\Query;

class MentalMapHistoryFetcher
{
    public function fetch(
        array $fragments,
        string $mentalMapId,
        int $userId,
        int $threshold,
        bool $repetitionMode = false
    ): array {
        if (!$repetitionMode) {
            $items = $this->createMentalMapHistory($fragments, $mentalMapId, $userId);
        }
        return array_map(static function (array $item) use ($threshold): array {
            $all = isset($item['all']) ? (int) $item['all'] : 0;
            return [
                'id' => $item['id'],
                'all' => $all,
                'hiding' => isset($item['hiding']) ? (int) $item['hiding'] : 0,
                'target' => isset($item['target']) ? (int) $item['target'] : 0,
                'done' => MentalMap::fragmentIsDone($all, $threshold),
                'seconds' => isset($item['seconds']) ? (int) $item['seconds'] : 0,
                'hidingPrev' => $item['hidingPrev'],
            ];
        }, $items ?? $fragments);
    }

    /**
     * @throws Exception
     */
    private function createMentalMapHistory(array $images, string $mentalMapId, int $userId): array
    {
        $history = array_map(static function (array $image): array {
            return [
                'id' => $image['id'],
                'all' => 0,
                'hiding' => 0,
                'hidingPrev' => 0,
                'target' => 0,
                'seconds' => 0,
            ];
        }, $images);

        $today = (new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow')))
                ->setTime(0, 0)
                ->format('U') . ' + (3 * 60 * 60)';
        $hidingBeforeQuery = (new Query())
            ->select(new Expression('MAX(t.text_hiding_percentage)'))
            ->from(['t' => 'mental_map_history'])
            ->where('t.image_fragment_id = h.image_fragment_id')
            ->andWhere('t.overall_similarity >= IFNULL(t.threshold, 0)')
            ->andWhere(['<=', new Expression('t.created_at'), new Expression($today)]);

        $rows = (new Query())
            ->select([
                'id' => 'h.image_fragment_id',
                'all' => 'MAX(h.overall_similarity)',
                'hiding' => 'MAX(h.text_hiding_percentage)',
                'hidingPrev' => $hidingBeforeQuery,
                'target' => 'MAX(h.text_target_percentage)',
                'seconds' => 'AVG(h.seconds)',
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
                $item['hidingPrev'] = (int) $rows[$item['id']]['hidingPrev'];
                $item['target'] = (int) $rows[$item['id']]['target'];
                $item['seconds'] = (int) $rows[$item['id']]['seconds'];
            }
            return $item;
        }, $history);
    }
}
