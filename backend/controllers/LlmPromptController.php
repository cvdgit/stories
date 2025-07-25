<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\LlmPrompt\LlmPrompt;
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
                        'roles' => [UserRoles::ROLE_ADMIN],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => LlmPrompt::find(),
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
}
