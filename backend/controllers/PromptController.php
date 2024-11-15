<?php

declare(strict_types=1);

namespace backend\controllers;

use common\rbac\UserRoles;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Request;
use yii\web\Response;

class PromptController extends Controller
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_EDITOR_ACCESS],
                    ],
                ],
            ],
        ]);
    }

    public function actionIndex(string $key): array
    {
        $prompts = (new Query())
            ->select(['id', 'name', 'prompt'])
            ->from(['t' => 'llm_prompt'])
            ->where(['t.key' => $key])
            ->orderBy(['t.name' => SORT_ASC])
            ->all();
        return ['success' => true, 'prompts' => $prompts];
    }

    /**
     * @throws Exception
     */
    public function actionSave(Request $request): array
    {
        $payload = Json::decode($request->rawBody);
        $prompt = $payload['prompt'];
        $promptId = $payload['id'];
        $promptName = $payload['name'] ?? '';

        $command = Yii::$app->db->createCommand();
        $columns = ['prompt' => $prompt];
        if ($promptName !== '') {
            $columns['name'] = $promptName;
        }
        $command->update('llm_prompt', $columns, ['id' => $promptId]);
        $command->execute();
        return ['success' => true];
    }

    public function actionCreate(Request $request): array
    {
        $payload = Json::decode($request->rawBody);
        $promptId = $payload['id'];
        $promptName = $payload['name'];
        $key = $payload['key'] ?? 'slide_text';
        $prompt = $payload['prompt'] ?? <<<TEXT
            Текст:
            ```
            {TEXT}
            ```
            Перепиши текст сохраняя его суть.
            TEXT;

        $command = Yii::$app->db->createCommand();
        $command->insert('llm_prompt', [
            'id' => $promptId,
            'name' => $promptName,
            'prompt' => $prompt,
            'created_at' => time(),
            'key' => $key,
        ]);
        $command->execute();

        $prompts = (new Query())
            ->select(['id', 'name', 'prompt'])
            ->from(['t' => 'llm_prompt'])
            ->where(['t.key' => $key])
            ->orderBy(['t.name' => SORT_ASC])
            ->all();
        return ['success' => true, 'prompts' => $prompts];
    }

    public function actionGet(string $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        return (new Query())
            ->select(['prompt', 'name'])
            ->from(['t' => 'llm_prompt'])
            ->where(['id' => $id])
            ->one();
    }
}
