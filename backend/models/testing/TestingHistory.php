<?php

declare(strict_types=1);

namespace backend\models\testing;

use yii\db\Query;

class TestingHistory
{

    public function getTestingStudents(int $testingId): array
    {
        $query = (new Query())
            ->select([
                'student_id' => 't.student_id',
                'student_name' => 't2.name',
                'items_count' => 'COUNT(t.id)',
            ])
            ->from(['t' => 'user_question_history'])
            ->innerJoin(['t2' => 'user_student'], 't.student_id = t2.id')
            ->where(['t.test_id' => $testingId])
            ->groupBy('t.student_id')
            ->orderBy(['t2.name' => SORT_ASC]);
        return $query->all();
    }

    public function getStudentTesting(int $studentId): array
    {
        $query = (new Query())
            ->select([
                'test_id' => 't.test_id',
                'test_name' => 't2.title',
                'items_count' => 'COUNT(t.id)',
            ])
            ->from(['t' => 'user_question_history'])
            ->innerJoin(['t2' => 'story_test'], 't.test_id = t2.id')
            ->where(['t.student_id' => $studentId])
            ->groupBy('t.test_id')
            ->orderBy(['t2.title' => SORT_ASC]);
        return $query->all();
    }

    public function getDetail(int $testId, int $studentId): array
    {
        $query = (new Query())
            ->select([
                'question_name' => 'question_history.entity_name',
                'correct' => 'question_history.correct_answer',
                'user_answers' => "GROUP_CONCAT(answers.answer_entity_name SEPARATOR ', ')",
                'question_created' => 'question_history.created_at',
            ])
            ->from(['t' => 'story_test'])
            ->innerJoin(['question_history' => 'user_question_history'], 'question_history.test_id = t.id')
            ->innerJoin(['answers' => 'user_question_answer'], 'question_history.id = answers.question_history_id')
            ->where(['t.id' => $testId])
            ->andWhere(['question_history.student_id' => $studentId])
            ->orderBy(['question_history.created_at' => SORT_ASC])
            ->groupBy('question_history.id');
        return $query->all();
    }
}
