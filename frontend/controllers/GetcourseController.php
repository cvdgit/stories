<?php

declare(strict_types=1);

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Request;

class GetcourseController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionWebhook(Request $request): void
    {
        $rawBody = $request->get();
        file_put_contents(Yii::getAlias('@public/upload/' . time()), var_export($rawBody, true));
    }
}
