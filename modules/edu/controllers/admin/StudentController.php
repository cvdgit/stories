<?php

declare(strict_types=1);

namespace modules\edu\controllers\admin;

use common\rbac\UserRoles;
use Exception;
use modules\edu\forms\admin\StudentSearch;
use modules\edu\forms\admin\StudentStoriesSearch;
use modules\edu\models\EduStudent;
use modules\edu\services\StudentService;
use modules\edu\services\StudentStatService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class StudentController extends Controller
{
    private $studentService;
    /**
     * @var StudentStatService
     */
    private $studentStatService;

    public function __construct($id, $module, StudentService $studentService, StudentStatService $studentStatService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->studentService = $studentService;
        $this->studentStatService = $studentStatService;
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

    public function actionIndex(Request $request): string
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search($request->get());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        if (EduStudent::findOne($id) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }
        try {
            $this->studentService->delete($id);
            Yii::$app->session->setFlash('success', 'Ученик успешно удален');
        }
        catch (Exception $exception) {
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }
        return $this->redirect(['/edu/admin/student/index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionStories(int $student_id, Request $request): string
    {
        if (($student = EduStudent::findOne($student_id)) === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $searchModel = new StudentStoriesSearch();
        $dataProvider = $searchModel->search($student->id, $request->get());
        return $this->render('stories', [
            'student' => $student,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClearStoryHistory(int $student_id, int $story_id): Response
    {
        try {
            $this->studentStatService->clearStoryHistory($student_id, $story_id);
            Yii::$app->session->setFlash('success', 'Успешно');
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }
        return $this->redirect(['stories', 'student_id' => $student_id]);
    }
}
