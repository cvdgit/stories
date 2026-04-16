<?php

declare(strict_types=1);

namespace modules\edu\StoryProgress;

use common\models\StoryStudentProgress;

class StoryProgressFetcher
{
    /**
     * @param array<array-key, int> $studentIds
     * @return array<int, bool>
     */
    public function fetchStudentsStoryStatus(int $storyId, array $studentIds): array
    {
        $progressModels = StoryStudentProgress::find()
            ->where(['in', 'student_id', $studentIds])
            ->andWhere(['story_id' => $storyId])
            ->all();
        return array_combine(
            array_map(static function (StoryStudentProgress $progress) {
                return $progress->student_id;
            }, $progressModels),
            array_map(static function (StoryStudentProgress $progress) {
                return $progress->statusIsDone();
            }, $progressModels),
        );
    }
}
