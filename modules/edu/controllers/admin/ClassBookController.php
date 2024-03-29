<?php

declare(strict_types=1);

namespace modules\edu\controllers\admin;

use common\models\UserStudent;
use common\rbac\UserRoles;
use Exception;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\services\ClassBookService;
use modules\edu\Teacher\ClassBook\ManageTopics\AdminManageTopicsFormAction;
use modules\edu\Teacher\ClassBook\ManageTopics\TopicAccessAction;
use modules\edu\widgets\StudentStatWidget;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ClassBookController extends Controller
{
    private $classBookService;

    public function __construct($id, $module, ClassBookService $classBookService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->classBookService = $classBookService;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_ADMIN],
                    ],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'manage-topics' => AdminManageTopicsFormAction::class,
            'save-topic-access' => TopicAccessAction::class,
        ];
    }

    public function actionIndex()
    {

        $dataProvider = new ActiveDataProvider([
            'query' => EduClassBook::find(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        if (($model = EduClassBook::findOne($id)) === null) {
            throw new NotFoundHttpException('Класс не найден');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getStudents(),
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
            ]
        ]);

        $programs = array_map(static function(EduClassProgram $classProgram): string {
            return $classProgram->program->name;
        }, $model->classPrograms);

        return $this->render('view', [
            'dataProvider' => $dataProvider,
            'classBook' => $model,
            'programs' => $programs,
        ]);
    }

    public function actionCreateUser(int $class_book_id, int $student_id): Response
    {
        if (($classBook = EduClassBook::findOne($class_book_id)) === null) {
            Yii::$app->session->setFlash('error', 'Класс не найден');
            return $this->redirect(['view', 'id' => $classBook->id]);
        }

        if (($student = UserStudent::findOne($student_id)) === null) {
            Yii::$app->session->setFlash('error', 'Ученик не найден');
            return $this->redirect(['view', 'id' => $classBook->id]);
        }

        try {
            $this->classBookService->createUserAndLinkToStudent($student);
            Yii::$app->session->setFlash('success', 'Пользователь успешно создан');
        }
        catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }

        return $this->redirect(['view', 'id' => $classBook->id]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionStat(int $class_book_id, int $student_id, int $class_program_id = null): string
    {
        if (($classBook = EduClassBook::findOne($class_book_id)) === null) {
            throw new NotFoundHttpException('Класс не найден');
        }

        if (($student = UserStudent::findOne($student_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $classPrograms = $classBook->classPrograms;
        if (count($classPrograms) === 0) {
            throw new BadRequestHttpException('Не удалось определить предметы для класса');
        }

        if ($class_program_id !== null && count($classPrograms) > 1) {
            $currentClassProgram = EduClassProgram::findOne($class_program_id);
        }
        else {
            $currentClassProgram = $classPrograms[0];
        }
        if ($currentClassProgram === null) {
            throw new NotFoundHttpException('Программа не найдена');
        }

        $classProgramItems = array_map(static function(EduClassProgram $program) use ($student, $classBook, $currentClassProgram) {
            return [
                'label' => $program->program->name,
                'url' => [
                    '/edu/admin/class-book/stat',
                    'student_id' => $student->id,
                    'class_book_id' => $classBook->id,
                    'class_program_id' => $program->id,
                ],
                'active' => $currentClassProgram && $currentClassProgram->id === $program->id,
            ];
        }, $classPrograms);

        return $this->render('stat', [
            'student' => $student,
            'classProgram' => $currentClassProgram,
            'classProgramItems' => $classProgramItems,

            'statWidget' => StudentStatWidget::widget([
                'classProgram' => $currentClassProgram,
                'classId' => $classBook->class->id,
                'student' => $student,
            ]),
        ]);
    }
}
