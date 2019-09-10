<?php


namespace backend\controllers;


use backend\models\video\CreateVideoForm;
use common\models\SlideVideo;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class VideoController extends Controller
{

    public function actionIndex()
    {
        $query = SlideVideo::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new CreateVideoForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createVideo();
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

}