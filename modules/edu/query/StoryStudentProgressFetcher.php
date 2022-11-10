<?php

declare(strict_types=1);

namespace modules\edu\query;

use yii\db\Query;

class StoryStudentProgressFetcher
{
    public function fetch(int $storyId, int $studentId): int
    {
        $progress = (new Query())
            ->select('progress')
            ->from('story_student_progress')
            ->where([
                'story_id' => $storyId,
                'student_id' => $studentId,
            ])
            ->scalar();
        return (int)$progress;
    }
}
