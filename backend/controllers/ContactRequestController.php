<?php

namespace backend\controllers;

use common\models\ContactRequest;
use common\rbac\UserRoles;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ContactRequestController extends Controller
{

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_CONTACT_REQUESTS],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ContactRequest::find(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDelete(int $id): Response
    {
        if (($model = ContactRequest::findOne($id)) === null) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        $model->delete();
        Yii::$app->session->setFlash('success', 'Запись успешно удалена');
        return $this->redirect(['index']);
    }
}
