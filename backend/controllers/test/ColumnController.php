<?php

declare(strict_types=1);

namespace backend\controllers\test;

use backend\components\BaseController;
use backend\Testing\Questions\Column\ColumnQuestionPayload;
use backend\Testing\Questions\Column\Create\ColumnQuestionCreateForm;
use backend\Testing\Questions\Column\Create\CreateColumnQuestionCommand;
use backend\Testing\Questions\Column\Create\CreateColumnQuestionHandler;
use backend\Testing\Questions\Column\Import\ImportColumnQuestionsForm;
use backend\Testing\Questions\Column\Update\ColumnQuestionUpdateForm;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
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

            $payload = new ColumnQuestionPayload($createForm->firstDigit, $createForm->secondDigit, $createForm->sign, $createForm->result);
            if ($payload->isMultiplySign()) {
                $payload = $payload->withSteps();
            }

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

        $currentPayload = ColumnQuestionPayload::fromPayload(Json::decode($questionModel->regions));

        $updateForm = new ColumnQuestionUpdateForm($questionModel);
        if ($updateForm->load($request->post())) {
            if (!$updateForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $questionModel->name = $updateForm->name;

            $payload = new ColumnQuestionPayload(
                $updateForm->firstDigit,
                $updateForm->secondDigit,
                $updateForm->sign,
                $updateForm->result,
            );
            if ($payload->isMultiplySign()) {
                $payload = $payload->withSteps();
            }

            if ((string) $currentPayload !== (string) $payload) {
                try {
                    if (!$questionModel->save()) {
                        throw new DomainException('Question save error');
                    }
                    return ['success' => true];
                } catch (Exception $exception) {
                    Yii::$app->errorHandler->logException($exception);
                    return ['success' => false, 'message' => $exception->getMessage()];
                }
            }

            $questionModel->regions = Json::encode($payload);

            StoryTestAnswer::deleteAll(['story_question_id' => $questionModel->id]);

            $answers = [
                [
                    'story_question_id' => $questionModel->id,
                    'name' => $payload->getResult(),
                    'order' => 1,
                    'is_correct' => true,
                ],
            ];

            foreach ($payload->getSteps() as $i => $step) {
                $answers[] = [
                    'story_question_id' => $questionModel->id,
                    'name' => $step['resultInt'],
                    'order' => $i + 1,
                    'is_correct' => true,
                ];
            }

            try {
                if (!$questionModel->save()) {
                    throw new DomainException('Question save error');
                }

                if (count($answers) > 0) {
                    $insertCommand = Yii::$app->db->createCommand();
                    $insertCommand->batchInsert('story_test_answer', ['story_question_id', 'name', 'order', 'is_correct'], $answers);
                    $insertCommand->execute();
                }

                return ['success' => true];
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
    public function actionImportForm(int $test_id): string
    {
        $test = $this->findModel(StoryTest::class, $test_id);
        $formModel = new ImportColumnQuestionsForm();
        return $this->renderAjax('_import_form', [
            'formModel' => $formModel,
            'testId' => $test->id,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionImport(int $test_id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $testModel = $this->findModel(StoryTest::class, $test_id);
        $formModel = new ImportColumnQuestionsForm();
        if ($formModel->load($request->post())) {
            if (!$formModel->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $sign = null;
            if (count($formModel->sign) === 1) {
                $sign = $formModel->sign[0];
            }

            for ($i = 0; $i < (int) $formModel->number; $i++) {

                if ($sign === null) {
                    $randSign = random_int(0, count($formModel->sign) - 1);
                    $questionSign = $formModel->sign[$randSign] ?? '';
                } else {
                    $questionSign = $sign;
                }

                if ($questionSign === '') {
                    continue;
                }

                $payload = null;

                if ($questionSign === '+') {
                    $firstDigit = random_int((int) $formModel->firstDigitMin, (int) $formModel->firstDigitMax);
                    $secondDigit = random_int((int) $formModel->secondDigitMin, (int) $formModel->secondDigitMax);
                    $payload = new ColumnQuestionPayload(
                        (string) $firstDigit,
                        (string) $secondDigit,
                        '+',
                        (string) ($firstDigit + $secondDigit)
                    );
                }

                if ($questionSign === '-') {
                    $secondDigit = random_int((int) $formModel->firstDigitMin, (int) $formModel->firstDigitMax);
                    $firstDigit = random_int($secondDigit, max($secondDigit, (int) $formModel->secondDigitMax));
                    $payload = new ColumnQuestionPayload(
                        (string) $firstDigit,
                        (string) $secondDigit,
                        '-',
                        (string) ($firstDigit - $secondDigit)
                    );
                }

                if ($questionSign === '*') {
                    $firstDigit = random_int((int) $formModel->firstDigitMin, (int) $formModel->firstDigitMax);
                    $secondDigit = random_int((int) $formModel->secondDigitMin, (int) $formModel->secondDigitMax);
                    $payload = (new ColumnQuestionPayload(
                        (string) $firstDigit,
                        (string) $secondDigit,
                        '*',
                        (string) ($firstDigit * $secondDigit)
                    ))->withSteps();
                }

                if ($payload === null) {
                    continue;
                }

                $this->createColumnQuestionHandler->handle(new CreateColumnQuestionCommand(
                    $testModel->id,
                    'Вычисли столбиком: ' . $payload->getFirstDigit() . ' ' . $payload->getSign() . ' ' . $payload->getSecondDigit(),
                    $payload->getResult(),
                    $payload,
                ));
            }
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'No data'];
    }
}
