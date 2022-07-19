<?php

namespace backend\services;

use common\models\StudentQuestionProgress;
use common\models\UserQuestionAnswer;
use common\models\UserQuestionHistory;
use common\services\TransactionManager;
use Yii;
use yii\db\Query;

class QuizHistoryService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function clearHistory(int $quizId, int $studentId): void
    {
        $ids = (new Query())
            ->select('id')
            ->from(UserQuestionHistory::tableName())
            ->where(['test_id' => $quizId])
            ->andWhere(['student_id' => $studentId])
            ->indexBy('id')
            ->all();
        if (count($ids) > 0) {
            $ids = array_keys($ids);
        }

        $command = Yii::$app->db->createCommand();

        $this->transactionManager->wrap(function() use ($ids, $command, $quizId, $studentId) {

            if (count($ids) > 0) {

                $command
                    ->delete(UserQuestionHistory::tableName(), ['id' => $ids])
                    ->execute();

                $command
                    ->delete(UserQuestionAnswer::tableName(), ['question_history_id' => $ids])
                    ->execute();
            }

            $command
                ->delete(StudentQuestionProgress::tableName(), [
                    'student_id' => $studentId,
                    'test_id' => $quizId,
                ])
                ->execute();
        });
    }
}
