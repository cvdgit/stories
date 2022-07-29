<?php

namespace modules\edu\controllers;

use common\models\User;
use common\models\UserStudent;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Cookie;

class StudentController extends Controller
{

    public function actionIndex()
    {

        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

        $students = $currentUser->students;
        if (count($students) === 0) {
            return $this->redirect(['/edu/parent/index']);
        }

        $readCookies = $this->request->cookies;
        $uidCookie = $readCookies->getValue('uid');

        if ($uidCookie === null) {

            $writeCookies = $this->response->cookies;
            $uid = Uuid::uuid4()->toString();
            $writeCookies->add(new Cookie([
                'name' => 'uid',
                'value' => $uid,
            ]));

            $firstStudent = $currentUser->students[0];

            Yii::$app->db->createCommand()
                ->insert('user_student_session', [
                    'uid' => $uid,
                    'user_id' => $currentUser->id,
                    'student_id' => $firstStudent->id,
                ])
                ->execute();

            $uidCookie = $uid;
        }

        $sessionRow = (new Query())
            ->select('*')
            ->from('user_student_session')
            ->where('uid = :uid', [':uid' => $uidCookie])
            ->one();

        if ($sessionRow !== false) {

            $student = UserStudent::findOne($sessionRow['student_id']);
            if ($student->isMain()) {
                return $this->redirect(['/edu/parent/index']);
            }
        }

        return $this->render('index', [
            'studentName' => $student->name,
        ]);
    }
}
