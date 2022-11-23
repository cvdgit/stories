<?php

declare(strict_types=1);

namespace modules\edu\query;

use yii\db\Expression;
use yii\db\Query;

class StudentStoryDurationFetcher
{
    public function fetch(int $studentId, int $storyId, string $targetDate): string
    {
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$targetDate 00:00:00')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$targetDate 23:59:59')");

        $timeQuery = (new Query())
            ->select([
                'session_time' => 'MAX(story_student_stat.created_at) - MIN(story_student_stat.created_at)',
            ])
            ->from('story_student_stat')
            ->where(['story_student_stat.story_id' => $storyId, 'story_student_stat.student_id' => $studentId])
            ->andWhere(['between', 'story_student_stat.created_at', $betweenBegin, $betweenEnd])
            ->groupBy('story_student_stat.session');

        $query = (new Query())
            ->select([
                'total_time' => 'SEC_TO_TIME(SUM(t.session_time))',
            ])
            ->from(['t' => $timeQuery]);

        return (string)$query->scalar();
    }
}
