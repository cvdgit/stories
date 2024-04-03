<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use PDO;
use Yii;
use yii\db\Exception;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class GameController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ]
        ];
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionView(int $id, Response $response): array
    {
        $command = Yii::$app->db->createCommand("SELECT data FROM game_data WHERE user_id=:id");
        $command->bindValue(":id", $id, PDO::PARAM_INT);
        $result = $command->queryOne();
        if ($result === false) {
            throw new NotFoundHttpException("Game data not found");
        }
        return Json::decode($result['data']);
    }

    /**
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function actionCreate(Request $request): array
    {
        $payload = Json::decode($request->rawBody);
        $id = $payload["id"] ?? null;
        if ($id === null) {
            throw new BadRequestHttpException("ID field must be set ");
        }

        $command = Yii::$app->db->createCommand();
        $command->insert("game_data", [
            "user_id" => $id,
            "data" => $payload,
        ]);
        $sql = $command->getRawSql();
        $sql .= " ON DUPLICATE KEY UPDATE `data`=VALUES(`data`)";
        $command->setSql($sql);
        $command->execute();

        return [];
    }
}
