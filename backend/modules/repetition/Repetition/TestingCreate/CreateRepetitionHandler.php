<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\TestingCreate;

use yii\db\Query;

class CreateRepetitionHandler
{
    public function handle(CreateRepetitionForm $command): void
    {
        $repetitionExists = (new Query())
            ->from('test_repetition')
            ->where([
                'test_id' => $command->test_id,
                'student_id' => $command->student_id,
                'done' => 0,
            ])
            ->exists();
        if ($repetitionExists) {
            throw new \DomainException('Повторение уже существует');
        }

        $schedule = (new Query())
            ->select([
                'scheduleId' => 'si.schedule_id',
                'itemId' => 'si.id',
                'itemHours' => 'si.hours',
            ])
            ->from(['si' => 'schedule_item'])
            ->where(['si.schedule_id' => $command->schedule_id])
            ->orderBy(['si.hours' => SORT_ASC])
            ->one();

        if (empty($schedule)) {
            throw new \DomainException('Не удалось найти элемент расписания');
        }

        \Yii::$app->db->createCommand()
            ->insert('test_repetition', [
                'test_id' => $command->test_id,
                'student_id' => $command->student_id,
                'schedule_item_id' => $schedule['itemId'],
                'created_at' => time(),
            ])
            ->execute();
    }
}
