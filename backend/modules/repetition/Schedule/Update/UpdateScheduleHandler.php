<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule\Update;

class UpdateScheduleHandler
{
    public function handle(UpdateScheduleCommand $command): void
    {
        \Yii::$app->db->createCommand()
            ->update('schedule', ['name' => $command->getScheduleName()], ['id' => $command->getScheduleId()])
            ->execute();
    }
}
