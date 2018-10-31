<?php

namespace backend\controllers;

use Yii;
use common\models\Story;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

class StoryController extends \yii\web\Controller
{
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new Story model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Story();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->initStory();
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Story::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Story model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения успешно сохранены');
            //$this->refresh();
            // return $this->redirect(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $model->alias]));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Story model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    public function actionGetfromdropbox($id)
    {
        $story = $this->findModel($id);
        $result = ['success' => '', 'error' => ''];
        try {
            $story->exportSlideFromDropBox();
            $story->save();
            $result['success'] = 'Успешно';
        }
        catch (Exception $ex) {
            $result['error'] = $ex->getMessage();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->asJson($result);
    }

    public function actionImages($id)
    {
        $model = $this->findModel($id);
        return $this->render('images', [
            'model' => $model,
            'images' => $model->getStoryImages(),
        ]);
    }

}
