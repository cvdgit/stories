<?php

declare(strict_types=1);

namespace modules\edu\query;

use yii\db\Expression;
use yii\db\Query;

class StudentStoryStatByDateFetcher
{
    public function fetch(int $studentId, array $programStoryIds = null): array
    {
        $subQuery = (new Query())
            ->select([
                'storyId' => 'story_id',
                'createdAt' => new Expression('MAX(created_at)'),
            ])
            ->from('story_student_stat')
            ->where(['student_id' => $studentId])
            ->groupBy(['story_id'])
            ->orderBy(['createdAt' => SORT_DESC]);

        $query = (new Query())
            ->select([
                'storyIds' => new Expression('GROUP_CONCAT(DISTINCT t.storyId)'),
                'targetDate' => new Expression("DATE_FORMAT(FROM_UNIXTIME(t.createdAt + (3 * 60 * 60)), '%Y-%m-%d')"),
            ])
            ->from(['t' => $subQuery])
            ->groupBy(['targetDate'])
            ->orderBy(['targetDate' => SORT_DESC]);

        if ($programStoryIds !== null) {
            $query->andWhere(['in', 't.storyId', $programStoryIds]);
        }

        return $query->all();
    }
}
