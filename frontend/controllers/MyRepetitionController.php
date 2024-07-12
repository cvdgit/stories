<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\StoryTest;
use modules\edu\RepetitionApiInterface;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class MyRepetitionController extends Controller
{
    /**
     * @var RepetitionApiInterface
     */
    private $repetitionApi;

    public function __construct($id, $module, RepetitionApiInterface $repetitionApi, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repetitionApi = $repetitionApi;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionIndex(): string
    {
        $this->getView()->setMetaTags(
            'Повторения',
            'Повторения',
            'Повторения',
            'Повторения',
        );

        $student = Yii::$app->user->identity->student();
        if ($student === null) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        $repetitionDataProvider = $this->repetitionApi->getRepetitionDataProvider($student->id);

        return $this->render('index', [
            'repetitionDataProvider' => $repetitionDataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionView(int $id): string
    {
        $testing = StoryTest::findOne($id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $student = Yii::$app->user->identity->student();
        if ($student === null) {
            throw new ForbiddenHttpException('Доступ запрещен');
        }

        return $this->render('view', [
            'testing' => $testing,
            'studentId' => $student->id,
        ]);
    }
}
