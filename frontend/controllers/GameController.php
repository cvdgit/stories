<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\components\UserController;
use yii\db\Query;
use yii\filters\Cors;
use yii\helpers\Json;
use yii\web\User as WebUser;

class GameController extends UserController
{
    public $layout = "game";

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }

    public function actionShow(WebUser $user): string
    {
        $defaultConfig = [
            "id" => $user->getId(),
            "health" => 300,
            "isAlive" => true,
            "sceneToLoad" => 3,
            "testSuccess" => false,
            "stories" => [],
        ];

        $data = (new Query())
            ->select("data")
            ->from("game_data")
            ->where([
                'user_id' => $user->getId()
            ])
            ->scalar();

        $config = $defaultConfig;
        if ($data) {
            $config = Json::decode($data);
        }

        return $this->render("show", [
            "config" => Json::encode($config),
        ]);
    }
}
