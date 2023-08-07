<?php

declare(strict_types=1);

namespace modules\edu\controllers\parent;

use common\models\UserStudent;
use Exception;
use modules\edu\components\StudentLoginGenerator;
use modules\edu\forms\student\StudentForm;
use modules\edu\models\EduClass;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduParentInvite;
use modules\edu\models\EduUser;
use modules\edu\query\StudentClassFetcher;
use modules\edu\services\StudentService;
use modules\edu\widgets\StudentStatWidget;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class DefaultController extends Controller
{
    private $studentService;
    private $studentClassFetcher;

    public function __construct($id, $module, StudentService $studentService, StudentClassFetcher $studentClassFetcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->studentService = $studentService;
        $this->studentClassFetcher = $studentClassFetcher;
    }

    public function actionIndex(): string
    {

        /** @var EduUser $currentUser */
        $currentUser = EduUser::findOne(Yii::$app->user->getId());

        $dataProvider = new ActiveDataProvider([
            'query' => $currentUser->getStudents(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateStudent(Request $request)
    {
        $formModel = new StudentForm();
        if ($formModel->load($request->post())) {

            if (!$formModel->validate()) {
                throw new \DomainException('Ошибка валидации');
            }

            try {
                $this->studentService->createStudentByParent(Yii::$app->user->getId(), EduUser::createUsername(), $formModel, StudentLoginGenerator::generateLogin(), StudentLoginGenerator::generatePassword());
                Yii::$app->session->setFlash('success', 'Ученик успешно создан');
                return $this->redirect(['index']);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', 'Ошибка при создании ученика');
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
     * @throws BadRequestHttpException
     */
    private function getStudentClass(int $classBookId = null, EduClass $class = null): EduClass
    {
        if ($classBookId !== null) {
            $classBook = EduClassBook::findOne($classBookId);
            if ($classBook === null) {
                throw new BadRequestHttpException('Класс не найден');
            }
            $studentClass = $classBook->class;
        } else {
            $studentClass = $class;
        }

        if ($studentClass === null) {
            throw new BadRequestHttpException('Не удалось определить класс');
        }

        return $studentClass;
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
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

        $studentClassBookId = $this->studentClassFetcher->fetch($student->id);
        $class = $this->getStudentClass($studentClassBookId, $student->class);

        $classPrograms = $class->eduClassPrograms;
        if (count($classPrograms) === 0) {
            throw new BadRequestHttpException('Программа обучения не найдена');
        }

        if ($classProgram === null && count($classPrograms) > 0) {
            $classProgram = $classPrograms[0];
        }

        return $this->render('stats', [
            'classProgram' => $classProgram,
            'classPrograms' => $classPrograms,
            'student' => $student,

            'statWidget' => StudentStatWidget::widget([
                'classProgram' => $classProgram,
                'classId' => $class->id,
                'student' => $student,
            ]),
        ]);
    }

    public function actionInvite(string $code): Response
    {
        try {
            if (Yii::$app->user->isGuest) {
                throw new ForbiddenHttpException('Необходимо авторизоваться');
            }

            if (($invite = EduParentInvite::findOne(['code' => $code])) === null) {
                throw new NotFoundHttpException('Приглашение не найдено');
            }

            if (($user = EduUser::findOne(['email' => $invite->email])) === null) {
                throw new NotFoundHttpException('Пользователь не найден');
            }

            $currentUserEmail = Yii::$app->user->identity->email;
            if (!$invite->isOwnerEmail($currentUserEmail)) {
                throw new ForbiddenHttpException('Отказано в доступе');
            }

            $this->studentService->setStudentParent($user->id, $invite);

            Yii::$app->session->setFlash('success', 'Операция выполнена успешно');
            return $this->redirect(['/edu/parent/default/index']);
        }
        catch (Exception $exception) {
            Yii::$app->session->setFlash('error', $exception->getMessage());
            return $this->redirect(['/']);
        }
    }
}
