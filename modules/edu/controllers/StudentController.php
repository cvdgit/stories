<?php

namespace modules\edu\controllers;

use common\models\User;
use common\models\UserStudent;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduLesson;
use modules\edu\models\EduProgram;
use modules\edu\models\EduTopic;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

class StudentController extends Controller
{

    public function actionIndex()
    {

        $student = Yii::$app->studentContext->getStudent();


        /** @var User $currentUser */
        //$currentUser = Yii::$app->user->identity;

/*        $students = $currentUser->students;
        if (count($students) === 0) {
            return $this->redirect(['/edu/parent/index']);
        }*/

        //$readCookies = $this->request->cookies;
        //$uidCookie = $readCookies->getValue('uid');

/*        if ($uidCookie === null) {

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
        }*/

/*        $sessionRow = (new Query())
            ->select('*')
            ->from('user_student_session')
            ->where('uid = :uid', [':uid' => $uidCookie])
            ->one();*/

        //if ($sessionRow !== false) {

            //$student = UserStudent::findOne($sessionRow['student_id']);
/*            if ($student->isMain()) {
                return $this->redirect(['/edu/parent/index']);
            }*/
        //}

        $classBooks = $student->classBooks;
        $classBook = $classBooks[0];

        $dataProvider = new ActiveDataProvider([
            'query' => $classBook->getPrograms(),
        ]);

        return $this->render('index', [
            'studentName' => $student->name,
            'dataProvider' => $dataProvider,
            'classId' => $classBook->class_id,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionTopic(int $id)
    {

        if (($topic = EduTopic::findOne($id)) === null) {
            throw new NotFoundHttpException('Тема не найдена');
        }

        $readCookies = $this->request->cookies;
        $uidCookie = $readCookies->getValue('uid');

        $sessionRow = (new Query())
            ->select('*')
            ->from('user_student_session')
            ->where('uid = :uid', [':uid' => $uidCookie])
            ->one();

        $student = UserStudent::findOne($sessionRow['student_id']);

        $classBooks = $student->classBooks;
        $classBook = $classBooks[0];

        $classProgram = EduClassProgram::findClassProgram($classBook->class_id, $topic->class_program_id);
        $topics = $classProgram->eduTopics;

        $dataProvider = new ActiveDataProvider([
            'query' => $topic->getEduLessons(),
        ]);

        return $this->render('topic', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'topics' => $topics,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionLesson(int $id)
    {
        if (($lesson = EduLesson::findOne($id)) === null) {
            throw new NotFoundHttpException('Урок не найден');
        }

        $student = Yii::$app->studentContext->getStudent();

        $classBooks = $student->classBooks;
        $classBook = $classBooks[0];

        $topic = $lesson->topic;

        $classProgram = EduClassProgram::findClassProgram($classBook->class_id, $topic->class_program_id);
        $topics = $classProgram->eduTopics;

        $dataProvider = new ActiveDataProvider([
            'query' => $lesson->getStories(),
        ]);

        return $this->render('lesson', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'topics' => $topics,
            'dataProvider' => $dataProvider,
            'lesson' => $lesson,
        ]);
    }
}
