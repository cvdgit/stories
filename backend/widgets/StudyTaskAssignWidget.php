<?php

namespace backend\widgets;

use backend\models\study_task\StudyTaskAssignForm;
use yii\base\Widget;

class StudyTaskAssignWidget extends Widget
{

    public $studyTaskID;
    public $studyGroupID;

    public function run()
    {
        $model = new StudyTaskAssignForm();
        $model->study_task_id = $this->studyTaskID;
        $model->study_group_id = $this->studyGroupID;
        return $this->render('task_assign', [
            'model' => $model,
        ]);
    }
}
