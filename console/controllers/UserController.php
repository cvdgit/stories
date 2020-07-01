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
            $this->stdout($model->username . ' - ' . count($model->students) . PHP_EOL);
        }
        $this->stdout('Done!' . PHP_EOL);
    }

}