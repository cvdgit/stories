<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public $layout = "@backend/modules/gpt/views/layout/gpt";
    public function actionIndex(): string
    {
        return $this->render("index");
    }
}
