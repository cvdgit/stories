<?php

namespace frontend\models\study_task;

use yii\base\Model;

class TaskContinueForm extends Model
{

    public $task_id;

    public function rules()
    {
        return [
            ['task_id', 'integer'],
        ];
    }

    public function continueTask(): void
    {

    }
}
