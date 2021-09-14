<?php

namespace common\models\study_task;

use common\components\BaseStatus;
use common\models\StudyTaskProgress;
use Yii;

class StudyTaskProgressStatus extends BaseStatus
{

    public const ASSIGNED = 0;
    public const PROGRESS = 1;
    public const DONE = 2;

    public static function asArray(): array
    {
        return [
            self::ASSIGNED => 'Назначен',
            self::PROGRESS => 'В процессе',
            self::DONE => 'Завершен',
        ];
    }

    public static function setStatus(int $studyTaskID, int $userID, int $status): void
    {
        $arr = self::asArray();
        if (!isset($arr[$status])) {
            throw new \DomainException('Unknown status');
        }
        $command = Yii::$app->db->createCommand();
        $command->update(StudyTaskProgress::tableName(), ['status' => $status], 'study_task_id = :task AND user_id = :user', [':task' => $studyTaskID, ':user' => $userID]);
        $command->execute();
    }
}
