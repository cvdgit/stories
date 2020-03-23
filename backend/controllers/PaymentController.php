<?php


namespace backend\controllers;


use common\models\Payment;
use common\rbac\UserRoles;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class PaymentController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_USERS],
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

    public function actionIndex(int $status)
    {
        $query = Payment::find()->orderBy('created_at DESC');
        $query->andWhere(['state' => $status]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 40],
        ]);
        return $this->render('index',[
            'status' => $status,
            'dataProvider' => $dataProvider,
        ]);
    }

}