<?php

declare(strict_types=1);

namespace backend\controllers\test;

use backend\components\BaseController;
use backend\Testing\Questions\Step\Create\CreateStepQuestionCommand;
use backend\Testing\Questions\Step\Create\CreateStepQuestionHandler;
use backend\Testing\Questions\Step\Create\StepQuestionCreateForm;
use backend\Testing\Questions\Step\StepPayload;
use backend\Testing\Questions\Step\Update\StepQuestionUpdateForm;
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

class StepController extends BaseController
{
    private $createStepQuestionHandler;

    public function __construct($id, $module, CreateStepQuestionHandler $createStepQuestionHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->createStepQuestionHandler = $createStepQuestionHandler;
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
        $createForm = new StepQuestionCreateForm();
        $createForm->name = 'Выполните задание';
        return $this->render('create', [
            'quizModel' => $testing,
            'formModel' => $createForm,
            'steps' => Json::encode([]),
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
        $createForm = new StepQuestionCreateForm();
        if ($createForm->load($request->post(), '')) {
            if (!$createForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }
            $steps = array_map(static function(array $step): StepPayload {
                return StepPayload::fromPayload($step);
            }, Json::decode($createForm->steps));
            try {
                $this->createStepQuestionHandler->handle(
                    new CreateStepQuestionCommand($testing->id, $createForm->name, $createForm->job, $steps),
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
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionUpdate(int $id): string
    {
        $questionModel = $this->findModel(StoryTestQuestion::class, $id);
        $quizModel = $questionModel->storyTest;
        $updateForm = new StepQuestionUpdateForm($questionModel);
        return $this->render('update', [
            'quizModel' => $quizModel,
            'formModel' => $updateForm,
            'questionModel' => $questionModel,
            'steps' => Json::encode($updateForm->steps),
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
        $updateForm = new StepQuestionUpdateForm($questionModel);
        if ($updateForm->load($request->post(), '')) {

            if (!$updateForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $questionModel->name = $updateForm->name;

            $payload = [
                'job' => $updateForm->job,
                'steps' => Json::decode($updateForm->steps),
            ];
            $questionModel->regions = Json::encode($payload);

            $existsAnswersIds = array_map(static function(StoryTestAnswer $a): array {
                return [
                    'step_id' => $a->region_id,
                    'answer_id' => $a->id,
                ];
            }, $questionModel->storyTestAnswers);

            $toInsertAnswers = [];
            $toUpdateAnswers = [];
            foreach ($payload['steps'] as $step) {
                $stepId = $step['id'];
                $answerExists = array_filter($existsAnswersIds, static function(array $existsAnswer) use ($stepId): bool {
                    return $existsAnswer['step_id'] === $stepId;
                });
                if (count($answerExists) === 0) {
                    $toInsertAnswers[] = StepPayload::fromPayload($step);
                } else {
                    $toUpdateAnswers[] = StepPayload::fromPayload($step);
                }
            }

            $toDeleteAnswers = [];
            foreach ($existsAnswersIds as $existsAnswerItem) {
                $exists = array_filter($payload['steps'], static function(array $step) use ($existsAnswerItem): bool {
                    return $step['id'] === $existsAnswerItem['step_id'];
                });
                if (count($exists) === 0) {
                    $toDeleteAnswers[] = $existsAnswerItem;
                }
            }

            if (count($toDeleteAnswers) > 0) {
                $toDeleteAnswers = array_map(static function(array $el): int {
                    return $el['answer_id'];
                }, $toDeleteAnswers);
            }

            try {
                if (!$questionModel->save()) {
                    throw new DomainException('Question save error');
                }

                if (count($toInsertAnswers) > 0) {
                    $toInsertRows = [];
                    foreach ($toInsertAnswers as $step) {
                        $toInsertRows[] = [
                            'story_question_id' => $questionModel->id,
                            'name' => $step->getStepCorrectValues(),
                            'order' => $step->getIndex(),
                            'is_correct' => true,
                            'region_id' => $step->getId(),
                        ];
                    }
                    $insertCommand = Yii::$app->db->createCommand();
                    $insertCommand->batchInsert('story_test_answer', ['story_question_id', 'name', 'order', 'is_correct', 'region_id'], $toInsertRows);
                    $insertCommand->execute();
                }

                if (count($toUpdateAnswers) > 0) {
                    foreach ($toUpdateAnswers as $step) {
                        $updateCommand = Yii::$app->db->createCommand();
                        $updateCommand->update('story_test_answer', ['name' => $step->getStepCorrectValues()], ['region_id' => $step->getId()]);
                        $updateCommand->execute();
                    }
                }

                if (count($toDeleteAnswers) > 0) {
                    $deleteCommand = Yii::$app->db->createCommand();
                    $deleteCommand->delete('story_test_answer', 'story_question_id = '. $questionModel->id. ' AND id IN ('.implode(',', $toDeleteAnswers).')');
                    $deleteCommand->execute();
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
