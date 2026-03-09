<?php

declare(strict_types=1);

namespace modules\edu\query;

use yii\db\Query;

class ClassProgramsByStoryFetcher
{
    public function fetch(int $storyId): array
    {
        $query = (new Query())
            ->select(['classProgramId' => 'cp.id'])
            ->from(['sl' => 'edu_lesson_story'])
            ->innerJoin(['l' => 'edu_lesson'], 'sl.lesson_id = l.id')
            ->innerJoin(['t' => 'edu_topic'], 'l.topic_id = t.id')
            ->innerJoin(['cp' => 'edu_class_program'], 't.class_program_id = cp.id')
            ->where(['sl.story_id' => $storyId]);
        $rows = $query->all();
        return array_map(
            static function (string $id): int {
                return (int) $id;
            },
            array_column($rows, 'classProgramId'),
        );
    }
}
