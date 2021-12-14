<?php

namespace frontend\controllers;

use backend\components\BaseController;

class TestMobileController extends BaseController
{

    public function actionView(int $id)
    {

        return $this->render('view');
    }
}