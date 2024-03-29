<?php

declare(strict_types=1);

namespace common\services;

use common\models\StoryTest;
use common\models\UserQuestionAnswer;
use common\models\UserQuestionHistory;
use Yii;
use yii\db\Query;

class TestDetailService
{
    public function getDetail(int $testId, int $studentId): array
    {
        $query = (new Query())
            ->select([
                'question_history.entity_name AS question_name',
                'question_history.correct_answer AS correct',
                "GROUP_CONCAT(answers.answer_entity_name SEPARATOR ', ') AS user_answers"
            ])
            ->from(['t' => StoryTest::tableName()])
            ->innerJoin(['question_history' => UserQuestionHistory::tableName()], 'question_history.test_id = t.id')
            ->leftJoin(['answers' => UserQuestionAnswer::tableName()], 'question_history.id = answers.question_history_id')
            ->where(['t.id' => $testId])
            ->andWhere(['question_history.student_id' => $studentId])
            ->orderBy(['question_history.created_at' => SORT_ASC])
            ->groupBy('question_history.id');
        return $query->all();
    }

    public function getIncorrectCount(int $testId, int $studentId): int
    {
        $query = (new Query())
            ->from(['t' => StoryTest::tableName()])
            ->innerJoin(['question_history' => UserQuestionHistory::tableName()], 'question_history.test_id = t.id')
            ->where(['t.id' => $testId, 'question_history.student_id' => $studentId])
            ->andWhere('question_history.correct_answer = 0');
        return (int)$query->count();
    }
}
