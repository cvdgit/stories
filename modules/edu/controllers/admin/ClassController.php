<?php

namespace modules\edu\controllers\admin;

use modules\edu\models\EduClass;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class ClassController extends Controller
{

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EduClass::find(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {

        return $this->render('create');
    }
}
