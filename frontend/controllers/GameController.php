<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\components\UserController;

class GameController extends UserController
{
    public $layout = "game";

    public function actionShow(): string
    {
        $defaultConfig = [
            "id" => 100,
            "health" => 300,
            "isAlive" => true,
            "sceneToLoad" => 3,
            "testSuccess" => true,
        ];

        return $this->render("show");
    }
}
