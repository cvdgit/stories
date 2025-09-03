<?php

declare(strict_types=1);

namespace frontend\Training;

use common\components\MentalMapThreshold;
use DateTimeInterface;
use phpQuery;
use yii\db\Expression;
use yii\db\Query;

final class MentalMapDayHistoryTargetWordsFetcher
{
    public function fetch(int $userId, DateTimeInterface $beginDate, DateTimeInterface $endDate, int $hours): array
    {
        $startDate = $beginDate->format('Y-m-d H:i:s');
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$startDate')");
        $finishDate = $endDate->format('Y-m-d H:i:s');
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$finishDate')");

        $hourExpression = new Expression("hour(FROM_UNIXTIME(h.created_at + (3 * 60 * 60)))");
        $minuteExpression = new Expression("minute(FROM_UNIXTIME(h.created_at + (3 * 60 * 60))) DIV $hours");

        $query = (new Query())
            ->select([
                'storyId' => 'h.story_id',
                'itemIds' => "GROUP_CONCAT(h.id SEPARATOR ',')",
                'storyTitle' => 's.title',
                'hour' => $hourExpression,
                'minute_div' => $minuteExpression,
            ])
            ->from(['h' => 'mental_map_history'])
            ->innerJoin(['s' => 'story'], 'h.story_id = s.id')
            ->where([
                'h.user_id' => $userId,
            ])
            ->andWhere(['between', new Expression('h.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
            ->andWhere(['>=', 'h.overall_similarity', MentalMapThreshold::DEFAULT_THRESHOLD])
            ->groupBy([
                'h.story_id',
                $hourExpression,
                $minuteExpression
            ])
            ->orderBy([
                'hour' => SORT_ASC,
                'minute_div' => SORT_ASC,
            ]);

        $repetitionQuery = (new Query())
            ->select([
                'storyId' => new Expression('0'),
                'itemIds' => "GROUP_CONCAT(h.id SEPARATOR ',')",
                'storyTitle' => new Expression("'Повторения ментальных карт'"),
                'hour' => $hourExpression,
                'minute_div' => $minuteExpression,
            ])
            ->from(['h' => 'mental_map_history'])
            ->where('h.story_id IS NULL')
            ->andWhere([
                'h.user_id' => $userId,
            ])
            ->andWhere(['between', new Expression('h.created_at + (3 * 60 * 60)'), $betweenBegin, $betweenEnd])
            ->andWhere(['>=', 'h.overall_similarity', MentalMapThreshold::DEFAULT_THRESHOLD])
            ->groupBy([
                'storyId',
                $hourExpression,
                $minuteExpression
            ])
            ->orderBy([
                'hour' => SORT_ASC,
                'minute_div' => SORT_ASC,
            ]);

        $rows = $query->union($repetitionQuery)->all();
        $processedData = [];
        foreach ($rows as $row) {

            $item = [
                'story_id' => $row['storyId'],
                'story_title' => $row['storyTitle'],
                'hour' => $row['hour'],
                'minute_div' => $row['minute_div'],
                'question_count' => 0,
            ];

            $itemIds = explode(',', $row['itemIds']);
            $contents = (new Query())
                ->select(['content' => 'h.content'])
                ->from(['h' => 'mental_map_history'])
                ->where(['in', 'h.id', $itemIds])
                ->all();

            $questionCount = 0;
            foreach ($contents as $content) {
                $questionCount += $this->calcTargetFromContent($content['content']);
            }

            $item['question_count'] = $questionCount;
            $processedData[] = $item;
        }

        return $processedData;
    }

    private function calcTargetFromContent(string $content): int
    {
        $document = phpQuery::newDocumentHTML($content);
        $elements = $document->find('.word-target.selected');
        $targetElements = $document->find('.target-text');
        if ($elements->length === 0 && $targetElements->length === 0) {
            return 1;
        }
        return $elements->length + $targetElements->length;
    }
}
