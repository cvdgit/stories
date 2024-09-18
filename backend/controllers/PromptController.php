<?php

declare(strict_types=1);

namespace backend\controllers;

use common\rbac\UserRoles;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Request;

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

    public function actionSave(Request $request): array
    {
        $payload = Json::decode($request->rawBody);
        $prompt = $payload['prompt'];
        $promptId = $payload['id'];

        $command = Yii::$app->db->createCommand();
        $command->update('llm_prompt', ['prompt' => $prompt], ['id' => $promptId]);
        $command->execute();
        return ['success' => true];
    }

    public function actionCreate(Request $request): array
    {
        $payload = Json::decode($request->rawBody);
        $promptId = $payload['id'];
        $promptName = $payload['name'];
        $prompt = <<<TEXT
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
            'key' => 'slide_text',
        ]);
        $command->execute();

        $prompts = (new Query())
            ->select(['id', 'name', 'prompt'])
            ->from(['t' => 'llm_prompt'])
            ->where("t.key = 'slide_text'")
            ->orderBy(['t.name' => SORT_ASC])
            ->all();
        return ['success' => true, 'prompts' => $prompts];
    }
}
