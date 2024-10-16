<?php

declare(strict_types=1);

namespace frontend\Training\FetchMentalMapHistoryTargetWords;

use DateTimeInterface;
use phpQuery;
use yii\db\Expression;
use yii\db\Query;

class MentalMapHistoryTargetWordsFetcher
{
    public function fetch(int $userId, DateTimeInterface $beginDate, DateTimeInterface $endDate): array
    {
        $weekStartDate = $beginDate->format('Y-m-d');
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$weekStartDate 00:00:00')");
        $weekEndDate = $endDate->format('Y-m-d');
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$weekEndDate 23:59:59')");

        $query = (new Query())
            ->select([
                'storyId' => 'h.story_id',
                'storyTitle' => 's.title',
                'historyContent' => 'h.content',
                'historyDate' => new Expression("DATE_FORMAT(FROM_UNIXTIME(h.created_at + (3 * 60 * 60)), '%Y-%m-%d')"),
            ])
            ->from(['h' => 'mental_map_history'])
            ->innerJoin(['s' => 'story'], 'h.story_id = s.id')
            ->where([
                'h.user_id' => $userId,
            ])
            ->andWhere(['between', 'h.created_at', $betweenBegin, $betweenEnd])
            ->orderBy(['h.created_at' => SORT_ASC]);

        $rows = $query->all();
        $processedData = [];
        foreach ($rows as $row) {
            $date = $row['historyDate'];
            if (!isset($processedData[$date])) {
                $processedData[$date] = [
                    'story_id' => $row['storyId'],
                    'story_title' => $row['storyTitle'],
                    'question_count' => 0,
                    'target_date' => $date,
                ];
            }

            $content = $row['historyContent'];
            if (empty($content)) {
                continue;
            }

            $processedData[$date]['question_count'] += $this->calcTargetFromContent($content);
        }

        return $processedData;
    }

    private function calcTargetFromContent(string $content): int
    {
        $document = phpQuery::newDocumentHTML($content);
        $elements = $document->find('.selected');
        if ($elements->length === 0) {
            return 1;
        }
        return $elements->length;
    }
}
