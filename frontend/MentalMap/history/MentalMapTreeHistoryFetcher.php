<?php

declare(strict_types=1);

namespace frontend\MentalMap\history;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use frontend\MentalMap\MentalMap;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use yii\db\Expression;
use yii\db\Query;

class MentalMapTreeHistoryFetcher
{
    public function fetch(
        string $mentalMapId,
        int $userId,
        array $treeData,
        int $threshold,
        bool $repetitionMode = false,
        bool $presentationMode = false
    ): array {
        $list = $this->flatten($treeData);
        if (!$repetitionMode) {
            $list = $this->createMentalMapTreeHistory(
                $list,
                $mentalMapId,
                $userId,
                $threshold,
                $presentationMode,
            );
        }
        return array_map(static function (array $item): array {
            return [
                'id' => $item['id'],
                'all' => $item['all'] ?? 0,
                'allTextClosed' => (int) ($item['allTextClosed'] ?? 0),
                'allTextClosedPrev' => (int) ($item['allTextClosedPrev'] ?? 0),
                'hiding' => isset($item['hiding']) ? (int) $item['hiding'] : 0,
                'target' => isset($item['target']) ? (int) $item['target'] : 0,
                'done' => $item['done'] ?? false,
                'seconds' => isset($item['seconds']) ? (int) $item['seconds'] : 0,
                'hidingPrev' => $item['hidingPrev'],
                'hiddenWords' => $item['hiddenWords'] ?? 0,
                'words' => $item['words'] ?? 0,
            ];
        }, $list);
    }

    private function createMentalMapTreeHistory(
        array $nodeList,
        string $mentalMapId,
        int $userId,
        int $threshold,
        bool $presentationMode = false
    ): array {
        $history = array_map(function (array $node) {
            $item = new HistoryItem(Uuid::fromString($node['id']));
            $text = $node['description'] ?? $node['title'];
            $item->setWords($this->countWords($text));
            return $item;
        }, $nodeList);

        $today = (new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow')))
                ->setTime(0, 0)
                ->format('U') . ' + (3 * 60 * 60)';

        $hidingBeforeQuery = (new Query())
            ->select(new Expression('MAX(t.text_hiding_percentage)'))
            ->from(['t' => 'mental_map_history'])
            ->where('t.image_fragment_id = h.image_fragment_id')
            ->andWhere('t.user_id = h.user_id')
            ->andWhere('t.overall_similarity >= IFNULL(t.threshold, 0)')
            ->andWhere(['<=', new Expression('t.created_at'), new Expression($today)]);

        $allBeforeQuery = (new Query())
            ->select(new Expression('MAX(t.all_hiding_percentage)'))
            ->from(['t' => 'mental_map_history'])
            ->where('t.image_fragment_id = h.image_fragment_id')
            ->andWhere('t.user_id = h.user_id')
            ->andWhere(['<=', new Expression('t.created_at'), new Expression($today)]);

        if ($presentationMode === false) {
            $query = (new Query())
                ->select([
                    'id' => 'h.image_fragment_id',
                    'all' => 'h.overall_similarity',
                    'allTextClosed' => 'h.all_hiding_percentage',
                    'allTextClosedPrev' => $allBeforeQuery,
                    'hiding' => 'h.text_hiding_percentage',
                    'hidingPrev' => $hidingBeforeQuery,
                    'target' => 'h.text_target_percentage',
                    'all_words' => 'h.all_important_words_included',
                    'seconds' => 'h.seconds',
                    'content' => 'h.content',
                ])
                ->from(['h' => 'mental_map_history'])
                ->where([
                    'h.mental_map_id' => $mentalMapId,
                    'h.user_id' => $userId,
                ])
                ->andWhere("h.overall_similarity >= IFNULL(h.threshold, $threshold)")
                ->andWhere('h.all_hiding_percentage = 0')
                ->orderBy(['hiding' => SORT_DESC]);
        } else {
            $query = (new Query())
                ->select([
                    'id' => 'stat.image_fragment_id',
                    'allTextClosed' => 'MAX(stat.all_hiding_percentage)',
                    'allTextClosedPrev' => $allBeforeQuery,
                ])
                ->from([
                    'h' => (new Query())
                        ->select([
                            'image_fragment_id' => 't1.image_fragment_id',
                            'maxCreatedAt' => 'MAX(t1.created_at)',
                            'user_id' => 't1.user_id',
                        ])
                        ->from(['t1' => 'mental_map_history'])
                        ->where([
                            't1.mental_map_id' => $mentalMapId,
                            't1.user_id' => $userId,
                        ])
                        ->andWhere('t1.all_hiding_percentage > 0')
                        ->groupBy(['t1.image_fragment_id'])
                ])
                ->innerJoin(
                    ['stat' => 'mental_map_history'],
                    'stat.image_fragment_id = h.image_fragment_id AND stat.created_at = h.maxCreatedAt'
                )
                ->groupBy(['stat.image_fragment_id'])
                ->indexBy('id');
            $rows = $query->all();
            return array_map(static function (HistoryItem $item) use ($rows) {
                if (isset($rows[$item->getId()->toString()])) {
                    $row = $rows[$item->getId()->toString()];
                    $all = (int) ($row['allTextClosed'] ?? 0);
                    return (new HistoryItem(
                        $item->getId(),
                        $all >= 100,
                        0,
                        $all,
                        (int) ($row['allTextClosedPrev'] ?? 0)
                    ))->toArray();
                }
                return $item->toArray();
            }, $history);
        }

        $historyRows = $query->all();

        $historyRowsByFragmentId = [];
        foreach ($historyRows as $historyRow) {
            $id = $historyRow['id'];
            if (!isset($historyRowsByFragmentId[$id])) {
                $historyRowsByFragmentId[$id] = [];
            }
            $historyRowsByFragmentId[$id][] = $historyRow;
        }

        $rows = $this->filterUserHistoryGroupByCorrect($historyRowsByFragmentId);

        return array_map(function (HistoryItem $item) use ($rows) {
            if (isset($rows[$item->getId()->toString()])) {
                $row = $rows[$item->getId()->toString()];
                $hiddenWords = $this->calcHiddenWordsFromContent($row['content']);
                return (new HistoryItem(
                    $item->getId(),
                    $row['done'],
                    (int) ($row['all'] ?? 0),
                    (int) ($row['allTextClosed'] ?? 0),
                    (int) ($row['allTextClosedPrev'] ?? 0),
                    (int) ($row['hiding'] ?? 0),
                    (int) ($row['hidingPrev'] ?? 0),
                    (int) ($row['seconds'] ?? 0),
                    (int) ($row['target'] ?? 0),
                    $hiddenWords,
                    $item->getWords(),
                ))->toArray();
            }
            return $item->toArray();
        }, $history);
    }

    private function filterUserHistoryGroupByCorrect(array $historyRowsByFragmentId): array
    {
        $rows = [];
        foreach ($historyRowsByFragmentId as $fragmentId => $rowsGroup) {
            $allWordsDoneItems = array_values(array_filter($rowsGroup, static function (array $groupItem): bool {
                return $groupItem['all_words'] === '1';
            }));
            if (count($allWordsDoneItems) > 0) {
                $rows[$fragmentId] = array_merge($allWordsDoneItems[0], ['done' => true]);
                continue;
            }

            $allWordsFailItems = array_filter($rowsGroup, static function (array $groupItem): bool {
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

    private function calcHiddenWordsFromContent(string $content): int
    {
        $document = \phpQuery::newDocumentHTML($content);
        return $document->find('.text-item-word.selected')->length;
    }

    private function countWords(string $text): int
    {
        if ($text === '') {
            return 0;
        }
        preg_match_all('/[\p{L}\p{N}]+(?:[-\'][\p{L}\p{N}]+)*/u', $text, $matches);
        return count($matches[0]);
    }
}
