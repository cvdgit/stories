<?php

declare(strict_types=1);

namespace backend\services;

use common\models\StudentQuestionProgress;
use common\models\UserQuestionAnswer;
use common\models\UserQuestionHistory;
use common\services\TransactionManager;
use Exception;
use frontend\events\RestartTestEvent;
use Yii;
use yii\db\Query;

class QuizHistoryService
{
    private $transactionManager;
    private $events = [];

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws Exception
     */
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

        $this->transactionManager->wrap(function () use ($ids, $quizId, $studentId): void {
            $command = Yii::$app->db->createCommand();
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

            $this->recordEvent(new RestartTestEvent($quizId, $studentId));
        });
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    private function recordEvent($event): void
    {
        $this->events[] = $event;
    }
}
