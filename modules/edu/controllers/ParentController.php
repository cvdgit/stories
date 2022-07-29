<?php

namespace modules\edu\controllers;

use common\models\User;
use common\models\UserStudent;
use Yii;
use yii\db\Query;
use yii\web\Controller;

class ParentController extends Controller
{

    public function actionIndex()
    {
        $readCookies = $this->request->cookies;
        $uidCookie = $readCookies->getValue('uid');

        if ($uidCookie === null) {
            return $this->redirect(['/edu/student/index']);
        }

        $sessionRow = (new Query())
            ->select('*')
            ->from('user_student_session')
            ->where('uid = :uid', [':uid' => $uidCookie])
            ->one();

        if ($sessionRow === false) {
            return $this->redirect(['/edu/student/index']);
        }

        $student = UserStudent::findOne($sessionRow['student_id']);
        if (!$student->isMain()) {
            return $this->redirect(['/edu/student/index']);
        }

        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

        $children = array_filter($currentUser->students, static function($student) {
            return !$student->isMain();
        });

        return $this->render('index', [
            'children' => $children,
        ]);
    }
}
