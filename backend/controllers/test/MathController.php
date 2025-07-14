<?php

declare(strict_types=1);

namespace backend\controllers\test;

use backend\components\BaseController;
use backend\Testing\Questions\Math\Create\CreateMathQuestionCommand;
use backend\Testing\Questions\Math\Create\CreateMathQuestionHandler;
use backend\Testing\Questions\Math\Create\MathQuestionCreateForm;
use backend\Testing\Questions\Math\MathPayload;
use backend\Testing\Questions\Math\Update\MathQuestionUpdateForm;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class MathController extends BaseController
{
    /**
     * @var CreateMathQuestionHandler
     */
    private $createMathQuestionHandler;
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    public function __construct($id, $module, CreateMathQuestionHandler $createMathQuestionHandler, TransactionManager $transactionManager, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->createMathQuestionHandler = $createMathQuestionHandler;
        $this->transactionManager = $transactionManager;
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
    public function actionCreate(int $test_id, int $gaps = null): string
    {
        $testing = StoryTest::findOne($test_id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $createForm = new MathQuestionCreateForm();
        $isGapsQuestion = (int) $gaps === 1;
        $createForm->name = $isGapsQuestion ? 'Заполните пропуски в задании' : 'Выполните задание';

        return $this->render('create', [
            'quizModel' => $testing,
            'formModel' => $createForm,
            'answers' => Json::encode([]),
            'fragments' => Json::encode([]),
            'isGapsQuestion' => $isGapsQuestion,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionCreateHandler(int $test_id, Request $request, Response $response, int $gaps = null): array
    {
        $response->format = Response::FORMAT_JSON;
        $testing = $this->findModel(StoryTest::class, $test_id);
        $createForm = new MathQuestionCreateForm();
        $isGapsQuestion = (int) $gaps === 1;
        if ($createForm->load($request->post(), '')) {
            if (!$createForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $isInputAnswer = (bool) $createForm->inputAnswer;

            $fragments = [];
            $answers = [];
            if ($createForm->fragments) {
                $fragments = Json::decode($createForm->fragments);
            } else {
                $answers = Json::decode($createForm->answers);
                foreach ($answers as $i => $answer) {
                    $answers[$i]['id'] = Uuid::uuid4()->toString();
                }
            }

            $payload = new MathPayload($createForm->job, $answers, $fragments, $isInputAnswer, $isGapsQuestion);

            try {
                $this->createMathQuestionHandler->handle(
                    new CreateMathQuestionCommand($testing->id, $createForm->name, $payload),
                );
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return ['success' => true, 'url' => Url::to(['test/update', 'id' => $testing->id, 'gaps' => $isGapsQuestion ? 1 : null])];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionUpdate(int $id, int $gaps = null): string
    {
        $questionModel = $this->findModel(StoryTestQuestion::class, $id);
        $quizModel = $questionModel->storyTest;
        $updateForm = new MathQuestionUpdateForm($questionModel);
        $isGapsQuestion = (int) $gaps === 1;
        return $this->render('update', [
            'quizModel' => $quizModel,
            'formModel' => $updateForm,
            'questionModel' => $questionModel,
            'answers' => Json::encode(array_map(static function(StoryTestAnswer $answer) use ($questionModel): array {
                return [
                    'id' => $answer->region_id,
                    'name' => $answer->name,
                    'correct' => $answer->is_correct === 1,
                    'questionId' => $questionModel->id,
                    'placeholder' => $answer->description,
                ];
            }, $questionModel->storyTestAnswers)),
            'fragments' => Json::encode($updateForm->fragments),
            'isGapsQuestion' => $isGapsQuestion,
        ]);
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionUpdateHandler(int $id, Request $request, Response $response, int $gaps = null): array
    {
        $response->format = Response::FORMAT_JSON;
        $questionModel = $this->findModel(StoryTestQuestion::class, $id);
        $isGapsQuestion = (int) $gaps === 1;
        $updateForm = new MathQuestionUpdateForm($questionModel);
        if ($updateForm->load($request->post(), '')) {

            if (!$updateForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $isInputAnswer = (bool) $updateForm->inputAnswer;

            $toInsertAnswers = [];
            $toUpdateAnswers = [];

            $answers = [];
            $fragments = [];

            if (!empty($updateForm->fragments)) {
                $fragments = Json::decode($updateForm->fragments);
                foreach ($fragments as $i => $fragment) {
                    foreach ($fragment['placeholders'] as $j => $placeholder) {
                        $isNew = ($placeholder['new'] ?? false) === true;
                        if ($isNew) {
                            unset($fragments[$i]['placeholders'][$j]['new']);
                            $toInsertAnswers[] = $placeholder;
                            continue;
                        }
                        $toUpdateAnswers[] = $placeholder;
                    }
                }
            } else {
                $answers = Json::decode($updateForm->answers);
                foreach ($answers as $i => $answer) {
                    $id = $answer['id'];
                    if ($id === '') {
                        $answers[$i]['id'] = Uuid::uuid4()->toString();
                        $toInsertAnswers[] = $answers[$i];
                        continue;
                    }
                    $toUpdateAnswers[] = $answer;
                }
            }

            $payload = (new MathPayload($updateForm->job, $answers, $fragments, $isInputAnswer, $isGapsQuestion));

            try {

                if (count($toInsertAnswers) > 0) {
                    $toInsertRows = [];
                    foreach ($toInsertAnswers as $insertAnswer) {
                        $toInsertRows[] = [
                            'story_question_id' => $questionModel->id,
                            'name' => $insertAnswer['value'],
                            'order' => 1,
                            'is_correct' => true,
                            'region_id' => null,
                            'description' => $insertAnswer['id']
                        ];
                    }
                    $insertCommand = Yii::$app->db->createCommand();
                    $insertCommand->batchInsert('story_test_answer', ['story_question_id', 'name', 'order', 'is_correct', 'region_id', 'description'], $toInsertRows);
                    $insertCommand->execute();
                }

                if (count($toUpdateAnswers) > 0) {
                    foreach ($toUpdateAnswers as $updateAnswer) {
                        $updateCommand = Yii::$app->db->createCommand();
                        $updateCommand->update(
                            'story_test_answer',
                            [
                                'name' => $updateAnswer['value'],
                                //'is_correct' => $updateAnswer['correct'] ? 1 : 0,
                                //'description' => $updateAnswer['id'] ?? null,
                            ],
                            ['description' => $updateAnswer['id']],
                        );
                        $updateCommand->execute();
                    }
                }

                /*if (count($toInsertAnswers) > 0) {
                    $toInsertRows = [];
                    foreach ($toInsertAnswers as $insertAnswer) {
                        $toInsertRows[] = [
                            'story_question_id' => $questionModel->id,
                            'name' => $insertAnswer['value'],
                            'order' => 1,
                            'is_correct' => $insertAnswer['correct'] ? 1 : 0,
                            'region_id' => $insertAnswer['id'],
                            'description' => $insertAnswer['placeholder'] ?? null
                        ];
                    }
                    $insertCommand = Yii::$app->db->createCommand();
                    $insertCommand->batchInsert('story_test_answer', ['story_question_id', 'name', 'order', 'is_correct', 'region_id', 'description'], $toInsertRows);
                    $insertCommand->execute();
                }

                if (count($toUpdateAnswers) > 0) {
                    foreach ($toUpdateAnswers as $updateAnswer) {
                        $updateCommand = Yii::$app->db->createCommand();
                        $updateCommand->update(
                            'story_test_answer',
                            [
                                'name' => $updateAnswer['value'],
                                'is_correct' => $updateAnswer['correct'] ? 1 : 0,
                                'description' => $updateAnswer['placeholder'] ?? null,
                            ],
                            ['region_id' => $updateAnswer['id']],
                        );
                        $updateCommand->execute();
                    }
                }*/

                $questionModel->regions = Json::encode($payload->asArray());
                if (!$questionModel->save()) {
                    throw new DomainException('Question save error');
                }

                return ['success' => true, 'answers' => $answers];

            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }

    /**
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionRemoveAnswer(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $questionId = $payload['questionId'] ?? null;
        $answerId = $payload['answerId'] ?? null;
        if (!$questionId || !$answerId) {
            throw new BadRequestHttpException('No ids');
        }
        $answer = StoryTestAnswer::findAnswerByRegionId($answerId);
        if ($answer === null) {
            throw new NotFoundHttpException('Answer not found');
        }

        $question = $this->findModel(StoryTestQuestion::class, (int) $questionId);
        $json = Json::decode($question->regions);
        $json['answers'] = array_filter($json['answers'], static function(array $a) use ($answerId): bool {
            return $a['id'] !== $answerId;
        });
        $question->regions = Json::encode($json);

        try {
            $this->transactionManager->wrap(static function() use ($question, $answer): void {
                if (!$question->save()) {
                    throw new DomainException('Question save error');
                }
                $answer->delete();
            });
            return ['success' => true];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}
