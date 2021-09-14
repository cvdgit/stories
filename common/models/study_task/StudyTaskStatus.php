<?php

namespace common\models\study_task;

use common\components\BaseStatus;
use common\models\StudyTask;
use Yii;

class StudyTaskStatus extends BaseStatus
{

    public const CLOSED = 0;
    public const OPEN = 1;

    public static function asArray(): array
    {
        return [
            self::CLOSED => 'Черновик',
            self::OPEN => 'Действующее',
        ];
    }

    public static function setStatus(int $id, int $status): void
    {
        $arr = self::asArray();
        if (!isset($arr[$status])) {
            throw new \DomainException('Unknown status');
        }
        $command = Yii::$app->db->createCommand();
        $command->update(StudyTask::tableName(), ['status' => $status], 'id = :id', [':id' => $id]);
        $command->execute();
    }

    public static function isOpen(StudyTask $task): bool
    {
        return $task->status === self::OPEN;
    }
}