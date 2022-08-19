<?php

namespace modules\edu\controllers\admin;

use yii\web\Controller;

class DefaultController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }
}
