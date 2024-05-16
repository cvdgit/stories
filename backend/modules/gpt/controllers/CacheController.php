<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use common\rbac\UserRoles;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Request;
use yii\web\Response;

class CacheController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_MODERATOR],
                    ],
                ],
            ],
        ];
    }

    public function actionGet(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $rawBody = Json::decode($request->rawBody);
        $slideTexts = $rawBody['slideTexts'];
        $md5 = md5($slideTexts);
        $data = (new Query())
            ->select('content')
            ->from('llm_cache')
            ->where(['key' => $md5])
            ->scalar();
        return ['success' => true, 'content' => $data];
    }

    public function actionSet(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $rawBody = Json::decode($request->rawBody);
        $slideTexts = $rawBody['slideTexts'];
        $content = $rawBody['content'];
        $md5 = md5($slideTexts);
        $command = Yii::$app->db->createCommand();
        $command->insert('llm_cache', [
            'key' => $md5,
            'content' => $content,
        ]);
        $command->execute();
        return ['success' => true];
    }
}
