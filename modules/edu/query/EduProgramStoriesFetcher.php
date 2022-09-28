<?php

declare(strict_types=1);

namespace modules\edu\query;

use Yii;
use yii\db\Query;

class EduProgramStoriesFetcher
{
    public function fetch(int $classId, int $programId): array
    {
        $query = (new Query())
            ->select([
                'storyId' => 's.id',
                'storyName' => 's.title',
                'lessonId' => 'el.id',
                'lessonName' => 'el.name',
                'topicId' => 'et.id',
                'topicName' => 'et.name',
            ])
            ->from(['ecp' => 'edu_class_program'])
            ->innerJoin(['ep' => 'edu_program'], 'ecp.program_id = ep.id')
            ->innerJoin(['et' => 'edu_topic'], 'ecp.id = et.class_program_id')
            ->innerJoin(['el' => 'edu_lesson'], 'et.id = el.topic_id')
            ->innerJoin(['els' => 'edu_lesson_story'], 'el.id = els.lesson_id')
            ->innerJoin(['s' => 'story'], 'els.story_id = s.id')
            ->where(['ecp.class_id' => $classId, 'ecp.program_id' => $programId])
            ->indexBy('storyId');
        return $query->all();
    }
}
