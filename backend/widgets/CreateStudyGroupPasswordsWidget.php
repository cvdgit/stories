<?php

namespace backend\widgets;

use yii\base\Widget;

class CreateStudyGroupPasswordsWidget extends Widget
{

    public $groupId;

    public function run()
    {
        return $this->render('_create_passwords', [
            'groupId' => $this->groupId,
        ]);
    }
}