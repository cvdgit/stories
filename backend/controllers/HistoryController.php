<?php

namespace backend\controllers;

use backend\models\StudentTestHistory;
use backend\models\testing\TestingHistory;
use common\models\StoryTest;
use common\models\StudentQuestionProgress;
use common\models\UserQuestionHistory;
use common\models\UserQuestionHistoryModel;
use common\models\UserStudent;
use common\rbac\UserRoles;
use common\services\TestHistoryService;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class HistoryController extends Controller
{

    private $historyService;
    private $testingHistory;

    public function __construct($id, $module, TestHistoryService $historyService, TestingHistory $testingHistory, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->historyService = $historyService;
        $this->testingHistory = $testingHistory;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_TEACHER],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $studentModel = $this->findStudentModel($id);
        $rows = $this->testingHistory->getStudentTesting($studentModel->id);
        return $this->render('view', [
            'student' => $studentModel,
            'rows' => $rows,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findStudentModel($id): UserStudent
    {
        if (($model = UserStudent::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Ученик не найден');
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findTestingModel($id): StoryTest
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Тест не найден');
    }

    public function actionClear(int $student_id, int $test_id)
    {
        $studentModel = $this->findStudentModel($student_id);
        $testModel = $this->findTestingModel($test_id);
        (new StudentTestHistory($studentModel->id))->clearTestHistory($testModel->id);
        Yii::$app->session->setFlash('success', 'История прохождения теста удалена');
        return $this->redirect(['view', 'id' => $student_id]);
    }

    public function actionClearAll(int $test_id)
    {
        $testModel = $this->findTestingModel($test_id);
        UserQuestionHistory::clearAllTestHistory($testModel->id);
        StudentQuestionProgress::resetProgressByTest($testModel->id);
        Yii::$app->session->setFlash('success', 'История прохождения теста удалена');
        return $this->redirect(['test/update', 'id' => $testModel->id]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionList(int $test_id): string
    {
        $testModel = $this->findTestingModel($test_id);
        $students = $this->testingHistory->getTestingStudents($testModel->id);
        return $this->render('list', [
            'test' => $testModel,
            'students' => $students,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionHistory(int $testing_id, int $student_id): string
    {
        $testing = $this->findTestingModel($testing_id);
        $student = $this->findStudentModel($student_id);
        $rows = $this->testingHistory->getDetail($testing->id, $student->id);
        return $this->render('history', [
            'testing' => $testing,
            'student' => $student,
            'rows' => $rows,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDetail(int $student_id, int $test_id): string
    {
        $student = $this->findStudentModel($student_id);
        $test = $this->findTestingModel($test_id);
        $model = new UserQuestionHistoryModel();
        $model->student_id = $student->id;
        return $this->render('detail', [
            'student' => $student,
            'test' => $test,
            'detail' => $model->getDetail($test->id),
        ]);
    }

    public function actionClearAllBySource(int $source)
    {
        try {
            $this->historyService->clearBySource($source);
            Yii::$app->session->setFlash('success', 'История прохождения теста удалена');
        }
        catch (Exception $ex) {
            Yii::$app->session->setFlash('error', str_replace("\n", '<br/>', $ex->getMessage()));
        }
        return $this->redirect(['test/index', 'source' => $source]);
    }
}
