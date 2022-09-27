<?php

declare(strict_types=1);

namespace modules\edu\controllers\parent;

use common\models\User;
use common\models\UserStudent;
use Exception;
use modules\edu\forms\student\StudentForm;
use modules\edu\models\EduClassProgram;
use modules\edu\services\StudentService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{

    private $studentService;

    public function __construct($id, $module, StudentService $studentService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->studentService = $studentService;
    }

    public function actionIndex(): string
    {

        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;

        $dataProvider = new ActiveDataProvider([
            'query' => $currentUser->getStudents()
                ->andWhere(['<>', 'status', UserStudent::STATUS_MAIN])
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateStudent()
    {
        $formModel = new StudentForm();
        if ($this->request->isPost && $formModel->load($this->request->post())) {
            try {
                $this->studentService->createStudent(Yii::$app->user->getId(), $formModel);
                Yii::$app->session->setFlash('success', 'Ученик успешно создан');
                return $this->redirect(['index']);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }
        return $this->render('create-student', [
            'formModel' => $formModel,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdateStudent(int $id)
    {
        if (($student = UserStudent::findOne($id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $studentForm = new StudentForm($student);
        if ($this->request->isPost && $studentForm->load($this->request->post())) {
            try {
                $this->studentService->updateStudent($student, $studentForm);
                Yii::$app->session->setFlash('success', 'Ученик успешно изменен');
                return $this->redirect(['index']);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }

        return $this->render('update-student', [
            'formModel' => $studentForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionStats(int $id, int $class_program_id = null): string
    {

        if (($student = UserStudent::findOne($id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $classProgram = null;
        if (($class_program_id !== null) && ($classProgram = EduClassProgram::findOne($class_program_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $class = $student->class;
        $classPrograms = $class->eduClassPrograms;

        if ($classProgram === null && count($classPrograms) > 0) {
            $classProgram = $classPrograms[0];
        }

        return $this->render('stats', [
            'classProgram' => $classProgram,
            'classPrograms' => $classPrograms,
            'student' => $student,
        ]);
    }
}
