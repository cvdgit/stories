<?php

declare(strict_types=1);

namespace modules\edu\controllers\teacher;

use common\models\User;
use Exception;
use modules\edu\forms\teacher\ClassBookForm;
use modules\edu\forms\student\StudentForm;
use modules\edu\forms\teacher\ParentInviteForm;
use modules\edu\models\EduClass;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduStudent;
use modules\edu\services\ParentInviteService;
use modules\edu\services\StudentService;
use modules\edu\services\TeacherService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class ClassBookController extends Controller
{
    private $teacherService;
    private $studentService;
    private $parentInviteService;

    public function __construct($id, $module, TeacherService $teacherService, StudentService $studentService, ParentInviteService $parentInviteService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->teacherService = $teacherService;
        $this->studentService = $studentService;
        $this->parentInviteService = $parentInviteService;
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
                $this->teacherService->createClassBook(Yii::$app->user->getId(), $formModel);
                return $this->redirect(['index']);
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
    public function actionUpdate(int $id, Request $request)
    {
        $model = $this->loadClassBook($id);

        $formModel = new ClassBookForm($model);

        if ($formModel->load($request->post()) && $formModel->validate()) {
            try {
                $this->teacherService->updateClassBook($model, $formModel);
                return $this->redirect(['index']);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->addFlash('error', 'При изменении класса произошла ошибка');
            }
        }

        $checkBoxList = [];
        foreach ($model->class->eduClassPrograms as $classProgram) {
            $checkBoxList[$classProgram->id] = $classProgram->program->name;
        }

        return $this->render('update', [
            'formModel' => $formModel,
            'checkBoxList' => $checkBoxList,
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

    /**
     * @throws NotFoundHttpException
     */
    public function actionParentInvite(int $student_id, Request $request, Response $response)
    {
        if (($student = EduStudent::findOne($student_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $inviteForm = new ParentInviteForm();
        if ($request->isPost && $inviteForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$inviteForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }
            try {
                $this->parentInviteService->sendInvite($student->id, $inviteForm);
                return ['success' => true];
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return $this->renderAjax('parent_invite', [
            'formModel' => $inviteForm,
        ]);
    }
}
