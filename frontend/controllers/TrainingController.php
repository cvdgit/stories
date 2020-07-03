<?php

namespace frontend\controllers;

use common\models\User;
use frontend\components\UserController;
use Yii;

class TrainingController extends UserController
{

    public function actionIndex()
    {
        $user = User::findModel(Yii::$app->user->id);
        return $this->render('index', [
            'students' => $user->getStudentsAsArray(),
        ]);
    }

}