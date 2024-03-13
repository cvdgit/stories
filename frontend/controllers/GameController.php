<?php

declare(strict_types=1);

namespace frontend\controllers;

use yii\web\Controller;

class GameController extends Controller
{
    public $layout = "game";

    public function actionShow(): string
    {
        return $this->render("show");
    }
}
