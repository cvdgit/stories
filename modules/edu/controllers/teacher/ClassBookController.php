<?php

declare(strict_types=1);

namespace modules\edu\controllers\teacher;

use common\models\User;
use Exception;
use modules\edu\forms\teacher\ClassBookForm;
use modules\edu\forms\student\StudentForm;
use modules\edu\models\EduClass;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\services\StudentService;
use modules\edu\services\TeacherService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ClassBookController extends Controller
{

    private $teacherService;
    private $studentService;

    public function __construct($id, $module, TeacherService $teacherService, StudentService $studentService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->teacherService = $teacherService;
        $this->studentService = $studentService;
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EduClassBook::findTeacherClassBooks(Yii::$app->user->getId()),
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $formModel = new ClassBookForm();
        if ($this->request->isPost && $formModel->load($this->request->post())) {
            try {
                $id = $this->teacherService->createClassBook(Yii::$app->user->getId(), $formModel);
                return $this->redirect(['index']);
                //return $this->redirect(['update', 'id' => $id]);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->addFlash('error', 'При создании класса произошла ошибка');
            }
        }
        return $this->render('create', [
            'formModel' => $formModel,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function loadClassBook(int $id): EduClassBook
    {
        if (($model = EduClassBook::findClassBook($id, Yii::$app->user->getId())) === null) {
            throw new NotFoundHttpException('Класс не найден');
        }
        return $model;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): string
    {
        $model = $this->loadClassBook($id);

        $formModel = new ClassBookForm($model);

        return $this->render('update', [
            'formModel' => $formModel,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionStudents(int $id): string
    {
        $model = $this->loadClassBook($id);

        $formModel = new ClassBookForm($model);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getStudents(),
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
            ]
        ]);

        return $this->render('students', [
            'formModel' => $formModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreateStudent(int $id)
    {
        $model = $this->loadClassBook($id);

        $formModel = new StudentForm();
        $formModel->class_id = $model->class_id;

        if ($this->request->isPost && $formModel->load($this->request->post())) {

            try {
                $this->studentService->createStudentWithUserAndAddToClassBook(User::createUsername(), $formModel, $model->id);
                Yii::$app->session->addFlash('success', 'Ученик успешно создан и добавлен в класс');
                return $this->redirect(['students', 'id' => $id]);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->addFlash('error', 'При создании ученика произошла ошибка');
            }
        }

        return $this->render('create-student', [
            'formModel' => $formModel,
            'classBook' => $model,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionPrograms(int $id): array
    {
        $this->response->format = Response::FORMAT_JSON;

        if (($class = EduClass::findOne($id)) === null) {
            throw new NotFoundHttpException('Класс не найден');
        }

        return array_map(static function(EduClassProgram $item) {
            return [
                'id' => $item->id,
                'name' => $item->program->name,
            ];
        }, $class->eduClassPrograms);
    }
}
