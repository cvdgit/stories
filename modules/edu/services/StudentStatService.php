<?php

declare(strict_types=1);

namespace modules\edu\services;

use Yii;

class StudentStatService
{
    public function clearStoryHistory(int $studentId, int $storyId): void
    {
        Yii::$app->db
            ->createCommand()
            ->delete('story_student_stat', ['story_id' => $storyId, 'student_id' => $studentId])
            ->execute();

        Yii::$app->db
            ->createCommand()
            ->delete('story_student_progress', ['story_id' => $storyId, 'student_id' => $studentId])
            ->execute();

        $rows = Yii::$app->db
            ->createCommand("SELECT DISTINCT test_id FROM story_story_test WHERE story_id = :story", [':story' => $storyId])
            ->queryAll();
        $testIds = array_column($rows, 'test_id');
        if (count($testIds) > 0) {

            Yii::$app->db
                ->createCommand()
                ->delete('user_question_history', ['student_id' => $studentId, 'test_id' => $testIds])
                ->execute();

            Yii::$app->db
                ->createCommand()
                ->delete('student_question_progress', ['student_id' => $studentId, 'test_id' => $testIds])
                ->execute();
        }
    }
}
