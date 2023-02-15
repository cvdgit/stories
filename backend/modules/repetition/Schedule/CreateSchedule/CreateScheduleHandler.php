<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule\CreateSchedule;

use common\services\TransactionManager;

class CreateScheduleHandler
{
    /** @var TransactionManager */
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function handle(CreateScheduleCommand $command): void
    {
        $this->transactionManager->wrap(function () use ($command) {

            $db = \Yii::$app->db;

            $db->createCommand()
                ->insert('schedule', [
                    'name' => $command->getScheduleName(),
                    'created_at' => time(),
                ])
                ->execute();

            $scheduleId = (int)$db->lastInsertID;

            $rows = [];
            foreach ($command->getScheduleHours() as $hours) {
                $rows[] = [
                    'hours' => $hours,
                    'schedule_id' => $scheduleId,
                ];
            }

            if (count($rows) === 0) {
                throw new \RuntimeException('Невозможно создать');
            }

            $db->createCommand()
                ->batchInsert('schedule_item', ['hours', 'schedule_id'], $rows)
                ->execute();
        });
    }
}
