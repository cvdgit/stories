<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\TestingDelete;

use yii\db\Query;

class DeleteHandler
{
    public function handle(DeleteRepetitionForm $command): void
    {
        $scheduleItemIds = (new Query())
            ->select('id')
            ->from('schedule_item')
            ->where(['schedule_id' => $command->schedule_id])
            ->all();
        $scheduleItemIds = array_column($scheduleItemIds, 'id');

        if (count($scheduleItemIds) === 0) {
            throw new \DomainException('Список элементов расписания пуст');
        }

        \Yii::$app->db->createCommand()
            ->delete('test_repetition', [
                'and', 'test_id = :tid AND student_id = :sid',
                ['in', 'schedule_item_id', $scheduleItemIds],
            ], [':tid' => $command->test_id, ':sid' => $command->student_id])
            ->execute();
    }
}
