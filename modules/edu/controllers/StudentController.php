<?php

namespace modules\edu\controllers;

use common\models\UserStudent;
use modules\edu\components\TopicAccessManager;
use modules\edu\models\EduClass;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduLesson;
use modules\edu\models\EduTopic;
use modules\edu\query\StudentClassFetcher;
use modules\edu\RepetitionApiInterface;
use modules\edu\widgets\StudentToolbarWidget;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class StudentController extends Controller
{
    /** @var TopicAccessManager */
    private $topicAccessManager;
    /** @var RepetitionApiInterface */
    private $repetitionApi;

    private $studentClassFetcher;

    public function __construct(
        $id,
        $module,
        TopicAccessManager $topicAccessManager,
        RepetitionApiInterface $repetitionApi,
        StudentClassFetcher $studentClassFetcher,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->topicAccessManager = $topicAccessManager;
        $this->repetitionApi = $repetitionApi;
        $this->studentClassFetcher = $studentClassFetcher;
    }

    /*    public function behaviors(): array
        {
            return [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => [UserRoles::ROLE_STUDENT],
                        ],
                    ],
                ],
            ];
        }*/

    /**
     * @throws ForbiddenHttpException|BadRequestHttpException
     */
    public function actionIndex(): string
    {
        $student = Yii::$app->studentContext->getStudent();
        if ($student === null) {
            $student = Yii::$app->user->identity->student();
            if ($student === null) {
                throw new ForbiddenHttpException('Доступ запрещен');
            }
        }

        $studentClassId = $this->studentClassFetcher->fetch($student->id);
        if ($studentClassId !== null) {
            $studentClass = EduClass::findOne($studentClassId);
        } else {
            $studentClass = $student->class;
            if ($studentClass === null) {
                throw new BadRequestHttpException('Не удалось определить класс');
            }
        }

        // Классы, в которых состоит ученик
        $classBooks = $student->classBooks;
        $classProgramIds = [];

        if (count($classBooks) === 0) {
            // Если ученик не состоит ни в одном классе
            $classProgramIds = array_map(static function($classProgram) {
                return $classProgram->id;
            }, $studentClass->eduClassPrograms);
        }
        else {
            foreach ($classBooks as $classBook) {
                $classProgramIds = array_merge($classProgramIds, $classBook->getClassProgramIds());
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => EduClassProgram::find()->where(['in', 'id', $classProgramIds]),
        ]);

        $repetitionDataProvider = $this->repetitionApi->getRepetitionDataProvider($student->id);

        return $this->render('index', [
            'student' => $student,
            'studentToolbarWidget' => $this->renderStudentToolbarWidget($student, $studentClass),
            'dataProvider' => $dataProvider,
            'repetitionDataProvider' => $repetitionDataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException|ForbiddenHttpException|BadRequestHttpException
     */
    public function actionTopic(int $id): string
    {
        if (($topic = EduTopic::findOne($id)) === null) {
            throw new NotFoundHttpException('Тема не найдена');
        }

        $student = Yii::$app->studentContext->getStudent();
        if ($student === null) {
            $student = Yii::$app->user->identity->student();
            if ($student === null) {
                throw new ForbiddenHttpException('Доступ запрещен');
            }
        }

        $studentClassId = $this->studentClassFetcher->fetch($student->id);
        if ($studentClassId !== null) {
            $studentClass = EduClass::findOne($studentClassId);
        } else {
            $studentClass = $student->class;
            if ($studentClass === null) {
                throw new BadRequestHttpException('Не удалось определить класс');
            }
        }

        $classProgram = $topic->classProgram;

        $dataProvider = new ActiveDataProvider([
            'query' => $topic->getEduLessons(),
        ]);

        $lessonAccess = $this->topicAccessManager->getStudentLessonAccess($classProgram->id, $student->id);

        return $this->render('topic', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'studentToolbarWidget' => $this->renderStudentToolbarWidget($student, $studentClass),
            'topics' => $classProgram->eduTopics,
            'dataProvider' => $dataProvider,
            'topic' => $topic,
            'lessonAccess' => $lessonAccess,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     */
    public function actionLesson(int $id): string
    {
        if (($lesson = EduLesson::findOne($id)) === null) {
            throw new NotFoundHttpException('Урок не найден');
        }

        $student = Yii::$app->studentContext->getStudent();
        if ($student === null) {
            $student = Yii::$app->user->identity->student();
            if ($student === null) {
                throw new ForbiddenHttpException('Доступ запрещен');
            }
        }

        $studentClassId = $this->studentClassFetcher->fetch($student->id);
        if ($studentClassId !== null) {
            $studentClass = EduClass::findOne($studentClassId);
        } else {
            $studentClass = $student->class;
            if ($studentClass === null) {
                throw new BadRequestHttpException('Не удалось определить класс');
            }
        }

        $this->canAccessTopic($lesson->topic_id, $lesson->id, $student->id);

        $dataProvider = new ActiveDataProvider([
            'query' => $lesson->getStories(),
        ]);

        $topic = $lesson->topic;
        $classProgram = $topic->classProgram;

        return $this->render('lesson', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'topics' => $classProgram->eduTopics,
            'dataProvider' => $dataProvider,
            'lesson' => $lesson,
            'currentTopicId' => $topic->id,
            'programId' => $classProgram->id,
            'studentToolbarWidget' => $this->renderStudentToolbarWidget($student, $studentClass),
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    private function canAccessTopic(int $topicId, int $lessonId, int $studentId): void
    {
        $classProgramId = (new Query())
            ->select('class_program_id')
            ->from('edu_topic')
            ->where(['id' => $topicId])
            ->scalar();
        if (empty($classProgramId)) {
            throw new NotFoundHttpException('Тема не найдена');
        }
        $lessonAccess = $this->topicAccessManager->getStudentLessonAccess($classProgramId, $studentId);
        if (!isset($lessonAccess[$lessonId])) {
            throw new NotFoundHttpException('Урок не найден');
        }
        if ($lessonAccess[$lessonId]['access'] === false) {
            throw new ForbiddenHttpException('Что бы получить доступ к урок - необходимо пройти предыдущие');
        }
    }

    private function renderStudentToolbarWidget(UserStudent $student, EduClass $class): string
    {
        return StudentToolbarWidget::widget(['studentName' => $student->name, 'studentClassName' => $class->name]);
    }
}
