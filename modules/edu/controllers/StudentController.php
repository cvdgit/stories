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
        $classBooks = $student->classBooks;

        $classProgramIds = [];
        foreach ($classBooks as $classBook) {
            $classProgramIds = array_merge($classProgramIds, $classBook->getClassProgramIds());
        }

        $dataProvider = new ActiveDataProvider([
            'query' => EduClassProgram::find()->where(['in', 'id', $classProgramIds]),
        ]);

        return $this->render('index', [
            'student' => $student,
            'dataProvider' => $dataProvider,
            'classBook' => $classBook,
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

        $student = Yii::$app->studentContext->getStudent();

/*        $classBooks = $student->classBooks;
        $classBook = $classBooks[0];

        $classProgram = EduClassProgram::findClassProgram($classBook->class_id, $topic->class_program_id);
        $topics = $classProgram->eduTopics;*/

        $classProgram = $topic->classProgram;

        $dataProvider = new ActiveDataProvider([
            'query' => $topic->getEduLessons(),
        ]);

        return $this->render('topic', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'topics' => $classProgram->eduTopics,
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
