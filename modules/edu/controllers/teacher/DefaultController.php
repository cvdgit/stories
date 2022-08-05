<?php

declare(strict_types=1);


namespace modules\edu\controllers\teacher;

use yii\web\Controller;

class DefaultController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }
}
