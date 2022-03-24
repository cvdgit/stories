<?php

namespace frontend\controllers;

use frontend\models\ContactRequestForm;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class ContactController extends Controller
{

    public function actionCreate()
    {
        $model = new ContactRequestForm();
        if ($model->load($this->request->post()) && $model->validate()) {
            $this->response->format = Response::FORMAT_JSON;
            try {
                $model->create();
                return ['success' => true];
            }
            catch (\Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return ['success' => false, 'message' => 'Произошла ошибка'];
            }
        }
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }
}