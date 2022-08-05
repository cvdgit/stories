<?php

namespace modules\edu\controllers;

use common\models\User;
use common\rbac\UserRoles;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Cookie;

/**
 * Default controller for the `edu` module
 */
class DefaultController extends Controller
{

    public function actionIndex()
    {

        if (Yii::$app->user->can(UserRoles::ROLE_TEACHER)) {
            $student = Yii::$app->studentContext->getStudent();
            if ($student === null) {
                return $this->redirect(['/edu/teacher/default/index']);
            }
            return $this->redirect(['/edu/student/index']);
        }

        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

        $students = $currentUser->students;
        if (count($students) === 0) {
            return $this->redirect(['/edu/parent/index']);
        }

        $readCookies = Yii::$app->request->cookies;
        $uidCookie = $readCookies->getValue('uid');

        if ($uidCookie === null) {

            $firstStudent = $students[0];

            $writeCookies = $this->response->cookies;
            $uid = Uuid::uuid4()->toString();
            $writeCookies->add(new Cookie([
                'name' => 'uid',
                'value' => $uid,
            ]));

            Yii::$app->db->createCommand()
                ->insert('user_student_session', [
                    'uid' => $uid,
                    'user_id' => $currentUser->id,
                    'student_id' => $firstStudent->id,
                ])
                ->execute();

            return $this->redirect(['/edu/student/index']);
        }

        $sessionRow = (new Query())
            ->select('*')
            ->from('user_student_session')
            ->where('uid = :uid', [':uid' => $uidCookie])
            ->one();

        if ($sessionRow === false) {

        }

        return $this->redirect(['/edu/student/index']);
    }

    public function actionSwitchToParent()
    {

        $readCookies = Yii::$app->request->cookies;

        $writeCookies = $this->response->cookies;
        if ($writeCookies->has('uid')) {
            $uidCookie = $writeCookies->get('uid');
            $writeCookies->remove($uidCookie);
        }

        $uid = Uuid::uuid4()->toString();
        $writeCookies->add(new Cookie([
            'name' => 'uid',
            'value' => $uid,
        ]));

        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

        $mainStudent = $currentUser->student();

        Yii::$app->db->createCommand()
            ->insert('user_student_session', [
                'uid' => $uid,
                'user_id' => $currentUser->id,
                'student_id' => $mainStudent->id,
            ])
            ->execute();

        return $this->redirect(['/edu/parent/index']);
    }
}
