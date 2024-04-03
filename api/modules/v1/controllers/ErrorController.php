<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class ErrorController extends Controller
{
    public function actionError()
    {
        return ["error" => "Not found"];
    }
}
