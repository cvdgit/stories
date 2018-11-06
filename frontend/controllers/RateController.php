<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\Rate;

class RateController extends \yii\web\Controller
{

    public function actionIndex()
    {
        $rates = Rate::find()->all();
        return $this->renderPartial('index', [
            'rates' => $rates,
        ]);
    }

    public function actionList()
    {
        return $this->renderPartial('list');
    }

    public function actionPayment()
    {
        // return $this->renderPartial('list');
    }

}
