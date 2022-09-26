<?php

declare(strict_types=1);

namespace modules\edu\controllers\admin;

use common\models\UserStudent;
use common\rbac\UserRoles;
use Exception;
use modules\edu\models\EduClassBook;
use modules\edu\services\ClassBookService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
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
    public function actionView(int $id)
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

        return $this->render('view', [
            'dataProvider' => $dataProvider,
            'classBook' => $model,
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
}
