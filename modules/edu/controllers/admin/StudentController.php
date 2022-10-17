<?php

declare(strict_types=1);

namespace modules\edu\controllers\admin;

use common\rbac\UserRoles;
use Exception;
use modules\edu\models\EduStudent;
use modules\edu\services\StudentService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StudentController extends Controller
{
    private $studentService;

    public function __construct($id, $module, StudentService $studentService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->studentService = $studentService;
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

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EduStudent::find()->with(['user', 'class']),
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);

        return $this->render('index', [
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
}
