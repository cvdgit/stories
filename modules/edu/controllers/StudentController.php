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
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class StudentController extends Controller
{

    /**
     * @throws ForbiddenHttpException
     */
    public function actionIndex(): string
    {

        if (($student = Yii::$app->studentContext->getStudent()) === null) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        $classBooks = $student->classBooks;

        $classProgramIds = [];
        if (count($classBooks) === 0) {

            $class = $student->class;
            $classProgramIds = array_map(static function($classProgram) {
                return $classProgram->id;
            }, $class->eduClassPrograms);
        }
        else {

            foreach ($classBooks as $classBook) {
                $classProgramIds = array_merge($classProgramIds, $classBook->getClassProgramIds());
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => EduClassProgram::find()->where(['in', 'id', $classProgramIds]),
        ]);

        return $this->render('index', [
            'student' => $student,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionTopic(int $id): string
    {

        if (($topic = EduTopic::findOne($id)) === null) {
            throw new NotFoundHttpException('Тема не найдена');
        }

        if (($student = Yii::$app->studentContext->getStudent()) === null) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        $classProgram = $topic->classProgram;

        $dataProvider = new ActiveDataProvider([
            'query' => $topic->getEduLessons(),
        ]);

        return $this->render('topic', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'topics' => $classProgram->eduTopics,
            'dataProvider' => $dataProvider,
            'topic' => $topic,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionLesson(int $id): string
    {
        if (($lesson = EduLesson::findOne($id)) === null) {
            throw new NotFoundHttpException('Урок не найден');
        }

        if (($student = Yii::$app->studentContext->getStudent()) === null) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        $topic = $lesson->topic;
        $classProgram = $topic->classProgram;

        $dataProvider = new ActiveDataProvider([
            'query' => $lesson->getStories(),
        ]);

        return $this->render('lesson', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'topics' => $classProgram->eduTopics,
            'dataProvider' => $dataProvider,
            'lesson' => $lesson,
            'currentTopicId' => $topic->id,
            'programId' => $classProgram->id,
        ]);
    }
}
