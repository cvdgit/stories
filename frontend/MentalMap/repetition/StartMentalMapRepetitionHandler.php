<?php

declare(strict_types=1);

namespace frontend\MentalMap\repetition;

use Yii;
use yii\db\Exception;
use yii\db\Query;

final class StartMentalMapRepetitionHandler
{
    /**
     * @throws Exception
     */
    public function handle(StartMentalMapRepetitionCommand $command): void
    {
        $schedule = (new Query())
            ->select([
                'scheduleId' => 's.id',
                'itemId' => 'i.id',
                'itemHours' => 'i.hours',
            ])
            ->from(['t' => 'mental_map'])
            ->innerJoin(['s' => 'schedule'], 't.schedule_id = s.id')
            ->innerJoin(['i' => 'schedule_item'], 's.id = i.schedule_id')
            ->where(['t.uuid' => $command->getMentalMapId()])
            ->orderBy(['i.hours' => SORT_ASC])
            ->one();
        if (empty($schedule)) {
            return;
        }

        $repetitionExists = (new Query())
            ->from('mental_map_repetition')
            ->where([
                'mental_map_id' => $command->getMentalMapId(),
                'student_id' => $command->getStudentId(),
                'schedule_item_id' => $schedule['itemId'],
            ])
            ->exists();

        if (!$repetitionExists) {
            Yii::$app->db
                ->createCommand()
                ->insert('mental_map_repetition', [
                    'mental_map_id' => $command->getMentalMapId(),
                    'student_id' => $command->getStudentId(),
                    'schedule_item_id' => $schedule['itemId'],
                    'created_at' => time(),
                ])
                ->execute();
        }
    }
}
