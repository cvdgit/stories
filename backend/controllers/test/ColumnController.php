<?php

declare(strict_types=1);

namespace backend\controllers\test;

use backend\components\BaseController;
use backend\Testing\Questions\Column\Create\ColumnQuestionCreateForm;
use backend\Testing\Questions\Column\Create\CreateColumnQuestionCommand;
use backend\Testing\Questions\Column\Create\CreateColumnQuestionHandler;
use backend\Testing\Questions\Column\Update\ColumnQuestionUpdateForm;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use DomainException;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class ColumnController extends BaseController
{
    /**
     * @var CreateColumnQuestionHandler
     */
    private $createColumnQuestionHandler;

    public function __construct($id, $module, CreateColumnQuestionHandler $createColumnQuestionHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->createColumnQuestionHandler = $createColumnQuestionHandler;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $test_id): string
    {
        $testing = StoryTest::findOne($test_id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тест не найден');
        }
        $createForm = new ColumnQuestionCreateForm();
        $createForm->name = 'Решите в столбик';
        return $this->render('create', [
            'quizModel' => $testing,
            'formModel' => $createForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionCreateHandler(int $test_id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $testing = $this->findModel(StoryTest::class, $test_id);
        $createForm = new ColumnQuestionCreateForm();
        if ($createForm->load($request->post())) {
            if (!$createForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $payload = [
                'firstDigit' => $createForm->firstDigit,
                'secondDigit' => $createForm->secondDigit,
                'sign' => $createForm->sign,
                'result' => $createForm->result,
            ];

            try {
                $this->createColumnQuestionHandler->handle(
                    new CreateColumnQuestionCommand($testing->id, $createForm->name, $createForm->result, $payload),
                );
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return [
                    'success' => true,
                    'url' => Url::to(['test/update', 'id' => $testing->id]),
                ];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): string
    {
        $questionModel = $this->findModel(StoryTestQuestion::class, $id);
        $quizModel = $questionModel->storyTest;
        $updateForm = new ColumnQuestionUpdateForm($questionModel);
        return $this->render('update', [
            'quizModel' => $quizModel,
            'formModel' => $updateForm,
            'questionModel' => $questionModel,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionUpdateHandler(int $id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $questionModel = $this->findModel(StoryTestQuestion::class, $id);
        $updateForm = new ColumnQuestionUpdateForm($questionModel);
        if ($updateForm->load($request->post())) {

            if (!$updateForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $questionModel->name = $updateForm->name;

            $payload = [
                'firstDigit' => $updateForm->firstDigit,
                'secondDigit' => $updateForm->secondDigit,
                'sign' => $updateForm->sign,
                'result' => $updateForm->result,
            ];
            $questionModel->regions = Json::encode($payload);

            $correctAnswer = $questionModel->storyTestAnswers[0];
            $correctAnswer->name = $updateForm->result;

            try {
                if (!$questionModel->save()) {
                    throw new DomainException('Question save error');
                }

                if (!$correctAnswer->save()) {
                    throw new DomainException('Answer save error');
                }

                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}
