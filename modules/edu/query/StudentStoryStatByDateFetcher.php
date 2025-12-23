<?php

declare(strict_types=1);

namespace modules\edu\query;

use yii\db\Expression;
use yii\db\Query;

class StudentStoryStatByDateFetcher
{
    public function fetch(int $studentId, array $programStoryIds = null): array
    {
        $query = (new Query())
            ->select([
                'storyIds' => new Expression('GROUP_CONCAT(DISTINCT story_id ORDER BY created_at DESC)'),
                'targetDate' => new Expression("DATE_FORMAT(FROM_UNIXTIME(created_at + (3 * 60 * 60)), '%Y-%m-%d')"),
            ])
            ->from('story_student_stat')
            ->where(['student_id' => $studentId])
            ->groupBy(['targetDate'])
            ->orderBy(['MAX(created_at)' => SORT_DESC]);

        if ($programStoryIds !== null) {
            $query->andWhere(['in', 'story_id', $programStoryIds]);
        }

        return $query->all();
    }
}
