<?php

declare(strict_types=1);

namespace modules\edu\controllers\parent;

use common\models\User;
use common\models\UserStudent;
use Exception;
use modules\edu\forms\student\StudentForm;
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

    public function actionIndex()
    {

        $student = Yii::$app->studentContext->getStudent();

        /*
        $readCookies = $this->request->cookies;
        $uidCookie = $readCookies->getValue('uid');

        if ($uidCookie === null) {
            return $this->redirect(['/edu/student/index']);
        }

        $sessionRow = (new Query())
            ->select('*')
            ->from('user_student_session')
            ->where('uid = :uid', [':uid' => $uidCookie])
            ->one();

        if ($sessionRow === false) {
            return $this->redirect(['/edu/student/index']);
        }

        $student = UserStudent::findOne($sessionRow['student_id']);
        if (!$student->isMain()) {
            return $this->redirect(['/edu/student/index']);
        }*/

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
                return $this->redirect(['index']);
            }
            catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
            }
        }

        return $this->render('create-student', [
            'formModel' => $formModel,
        ]);
    }
}
