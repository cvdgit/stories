<?php

declare(strict_types=1);

namespace modules\edu\query;

use Yii;
use yii\db\Expression;
use yii\db\Query;

class StudentProgramLastActivityDateFetcher
{
    public function fetch(array $studentIds, array $storyIds): array
    {
        if (count($studentIds) === 0) {
            return [];
        }

        if (count($storyIds) === 0) {
            $result = [];
            foreach ($studentIds as $studentId) {
                $result[$studentId] = '-';
            }
            return $result;
        }

        $query = (new Query())
            ->select([
                'studentId' => 'student_id',
                'lastActivity' => new Expression('MAX(updated_at)')
            ])
            ->from('story_student_progress')
            ->where(['in', 'student_id', $studentIds])
            ->andWhere(['in', 'story_id', $storyIds])
            ->groupBy(['student_id', 'story_id'])
            ->indexBy('studentId');
        $rows = $query->all();

        $result = [];
        foreach ($studentIds as $studentId) {
            $result[$studentId] = $rows[$studentId]['lastActivity'] ?? '-';
            if ($result[$studentId] !== '-') {
                $result[$studentId] = Yii::$app->formatter->asDate($result[$studentId]);
            }
        }
        return $result;
    }
}
