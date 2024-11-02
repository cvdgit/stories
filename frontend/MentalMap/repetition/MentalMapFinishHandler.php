<?php

declare(strict_types=1);

namespace frontend\MentalMap\repetition;

use common\services\TransactionManager;
use Yii;
use yii\db\Exception;
use yii\db\Query;

class MentalMapFinishHandler
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws Exception
     */
    public function handle(MentalMapFinishCommand $command): void
    {
        $scheduleId = (new Query())
            ->select('schedule_id')
            ->from('mental_map')
            ->where(['uuid' => $command->getMentalMapId()])
            ->scalar();

        $lastScheduleItem = (new Query())
            ->select([
                'itemId' => 'i.id',
                'itemHours' => 'i.hours',
                'done' => 'r.done',
            ])
            ->from(['r' => 'mental_map_repetition'])
            ->innerJoin(['i' => 'schedule_item'], 'r.schedule_item_id = i.id')
            ->where([
                'r.mental_map_id' => $command->getMentalMapId(),
                'r.student_id' => $command->getStudentId(),
            ])
            ->andWhere(['i.schedule_id' => $scheduleId])
            ->orderBy(['r.created_at' => SORT_DESC])
            ->one();

        if (empty($lastScheduleItem)) {
            throw new \DomainException('Запись в истории повторений не найдена');
        }

        //$done = (int)$lastScheduleItem['done'];
        //if ($done === 0) {
        //    throw new \DomainException('Попытка создания следующего повторения, когда не пройдено предыдущее');
        //}

        $nextScheduleItem = (new Query())
            ->select(['id', 'hours'])
            ->from('schedule_item')
            ->where(['schedule_id' => $scheduleId])
            ->andWhere(['>', 'hours', $lastScheduleItem['itemHours']])
            ->andWhere(['<>', 'id', $lastScheduleItem['itemId']])
            ->orderBy(['hours' => SORT_ASC])
            ->one();

        if ($nextScheduleItem) {
            $this->transactionManager->wrap(static function () use ($command, $lastScheduleItem, $nextScheduleItem) {
                Yii::$app->db
                    ->createCommand()
                    ->insert('mental_map_repetition', [
                        'mental_map_id' => $command->getMentalMapId(),
                        'student_id' => $command->getStudentId(),
                        'schedule_item_id' => $nextScheduleItem['id'],
                        'created_at' => time(),
                    ])
                    ->execute();

                Yii::$app->db
                    ->createCommand()
                    ->update('mental_map_repetition', ['done' => 1], [
                        'schedule_item_id' => $lastScheduleItem,
                        'mental_map_id' => $command->getMentalMapId(),
                        'student_id' => $command->getStudentId(),
                    ])
                    ->execute();
            });
        } else {
            Yii::$app->db
                ->createCommand()
                ->update('mental_map_repetition', ['done' => 1], [
                    'schedule_item_id' => $lastScheduleItem,
                    'mental_map_id' => $command->getMentalMapId(),
                    'student_id' => $command->getStudentId(),
                ])
                ->execute();
        }
    }
}
