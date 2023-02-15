<?php

declare(strict_types=1);

namespace frontend\modules\repetition\listeners;

use frontend\events\StudentTestingFinish;
use yii\db\Query;

class StartRepetitionListener
{
    public function handle(StudentTestingFinish $event): void
    {
        $schedule = (new Query())
            ->select([
                'scheduleId' => 's.id',
                'itemId' => 'i.id',
                'itemHours' => 'i.hours',
            ])
            ->from(['t' => 'story_test'])
            ->innerJoin(['s' => 'schedule'], 't.schedule_id = s.id')
            ->innerJoin(['i' => 'schedule_item'], 's.id = i.schedule_id')
            ->where(['t.id' => $event->getTestingId()])
            ->orderBy(['i.hours' => SORT_ASC])
            ->one();
        if (empty($schedule)) {
            return;
        }

        $repetitionExists = (new Query())
            ->from('test_repetition')
            ->where([
                'test_id' => $event->getTestingId(),
                'student_id' => $event->getStudentId(),
                'schedule_item_id' => $schedule['itemId'],
            ])
            ->exists();

        if (!$repetitionExists) {
            \Yii::$app->db->createCommand()
                ->insert('test_repetition', [
                    'test_id' => $event->getTestingId(),
                    'student_id' => $event->getStudentId(),
                    'schedule_item_id' => $schedule['itemId'],
                    'created_at' => time(),
                ])
                ->execute();
        }
    }
}
