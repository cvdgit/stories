<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\LlmPrompt\CreatePromptForm;
use backend\LlmPrompt\LlmPrompt;
use backend\LlmPrompt\UpdatePromptForm;
use common\rbac\UserRoles;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class LlmPromptController extends Controller
{
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

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => LlmPrompt::find(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGet(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $query = (new Query())
            ->select('*')
            ->from(['t' => 'llm_prompt'])
            ->where(['t.key' => 'mental-map-tree'])
            ->orderBy(['t.created_at' => SORT_DESC]);
        return [
            'prompts' => $query->all(),
        ];
    }

    public function actionCreate(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $command = Yii::$app->db->createCommand();
        $command->insert('llm_prompt', [
            'id' => Uuid::uuid4()->toString(),
            'name' => $payload['name'],
            'prompt' => $payload['prompt'],
            'created_at' => time(),
            'key' => 'mental-map-tree',
        ]);
        $command->execute();
        return [
            'success' => true,
        ];
    }

    /**
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function actionUpdate(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);

        $promptId = $payload['id'] ?? null;
        if ($promptId === null) {
            throw new BadRequestHttpException('No prompt id');
        }

        $command = Yii::$app->db->createCommand();
        $command->update('llm_prompt', [
            'name' => $payload['name'],
            'prompt' => $payload['prompt'],
        ], ['id' => $promptId]);
        $command->execute();

        return [
            'success' => true,
        ];
    }

    public function actionCreateForm(Request $request)
    {
        $createForm = new CreatePromptForm();
        if ($createForm->load($request->post()) && $createForm->validate()) {
            try {
                $prompt = LlmPrompt::create(
                    Uuid::uuid4(),
                    $createForm->name,
                    $createForm->key,
                    $createForm->prompt,
                );
                if (!$prompt->save()) {
                    throw new \DomainException('Prompt save error');
                }
                return $this->redirect(['index']);
            } catch (\Exception $exception) {
                Yii::$app->session->setFlash('error', $exception->getMessage());
                return $this->refresh();
            }
        }
        return $this->render('create-form', [
            'formModel' => $createForm,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionUpdateForm(string $id, Request $request)
    {
        if (!Uuid::isValid($id)) {
            throw new BadRequestHttpException('Invalid id');
        }

        $llmPrompt = LlmPrompt::findOne($id);
        if ($llmPrompt === null) {
            throw new NotFoundHttpException('Prompt not found');
        }

        $updateForm = new UpdatePromptForm([
            'name' => $llmPrompt->name,
            'key' => $llmPrompt->key,
            'prompt' => $llmPrompt->prompt,
        ]);

        if ($updateForm->load($request->post()) && $updateForm->validate()) {
            try {
                $llmPrompt->updatePrompt(
                    $updateForm->name,
                    $updateForm->key,
                    $updateForm->prompt
                );
                if (!$llmPrompt->save()) {
                    throw new \DomainException('Prompt save error');
                }
                Yii::$app->session->setFlash('success', 'Успешно');
                return $this->refresh();
            } catch (\Exception $exception) {
                Yii::$app->session->setFlash('error', $exception->getMessage());
                return $this->refresh();
            }
        }
        return $this->render('update-form', [
            'formModel' => $updateForm,
        ]);
    }
}
