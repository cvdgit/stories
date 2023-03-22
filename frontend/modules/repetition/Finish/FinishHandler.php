<?php

declare(strict_types=1);

namespace frontend\modules\repetition\Finish;

use common\services\TransactionManager;
use yii\db\Query;

class FinishHandler
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function handle(FinishForm $command): void
    {
        $scheduleId = (new Query())
            ->select('schedule_id')
            ->from('story_test')
            ->where(['id' => $command->test_id])
            ->scalar();

        $lastScheduleItem = (new Query())
            ->select([
                'itemId' => 'i.id',
                'itemHours' => 'i.hours',
                'done' => 'r.done',
            ])
            ->from(['r' => 'test_repetition'])
            ->innerJoin(['i' => 'schedule_item'], 'r.schedule_item_id = i.id')
            ->where([
                'r.test_id' => $command->test_id,
                'r.student_id' => $command->student_id,
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
            $this->transactionManager->wrap(static function() use ($command, $lastScheduleItem, $nextScheduleItem) {

                \Yii::$app->db->createCommand()
                    ->insert('test_repetition', [
                        'test_id' => $command->test_id,
                        'student_id' => $command->student_id,
                        'schedule_item_id' => $nextScheduleItem['id'],
                        'created_at' => time(),
                    ])
                    ->execute();

                \Yii::$app->db->createCommand()
                    ->update('test_repetition', ['done' => 1], ['schedule_item_id' => $lastScheduleItem])
                    ->execute();
            });
        } else {
            \Yii::$app->db->createCommand()
                ->update('test_repetition', ['done' => 1], ['schedule_item_id' => $lastScheduleItem])
                ->execute();
        }
    }
}
