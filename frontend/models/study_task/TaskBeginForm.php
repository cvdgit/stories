<?php

namespace frontend\models\study_task;

use common\models\study_task\StudyTaskProgressStatus;
use common\models\StudyTaskProgress;
use yii\base\Model;

class TaskBeginForm extends Model
{

    public $task_id;

    public function rules()
    {
        return [
            ['task_id', 'integer'],
        ];
    }

    public function beginTask(int $userID): void
    {
        if (!$this->validate()) {
            throw new \DomainException('TaskBeginForm not valid');
        }
        $taskProgressModel = StudyTaskProgress::create($this->task_id, $userID, StudyTaskProgressStatus::PROGRESS);
        $taskProgressModel->save();
    }
}
