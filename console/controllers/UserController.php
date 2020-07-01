<?php

namespace console\controllers;

use common\models\User;
use yii\console\Controller;

class UserController extends Controller
{

    public function actionCreateStudents()
    {
        $models = User::find()->with('students')->all();
        foreach ($models as $model) {
            $needCreate = false;
            if (count($model->students) === 0) {
                $needCreate = true;
            }
            else {
                $haveMain = false;
                foreach ($model->students as $student) {
                    if ($student->isMain()) {
                        $haveMain = true;
                    }
                }
                $needCreate = !$haveMain;
            }
            $this->stdout($model->username . ' - ' . var_export($needCreate, true) . PHP_EOL);
        }
        $this->stdout('Done!' . PHP_EOL);
    }

}