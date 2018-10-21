<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\User;

class UserController extends \yii\web\Controller
{

	public function behaviors()
	{
	    return [
	        'access' => [
	            'class' => AccessControl::className(),
	            'rules' => [
	                [
	                    'allow' => true,
	                    'roles' => ['author'],
	                ],
	            ],
	        ],
	    ];
	}

    public function actionProfile()
    {
    	$model = $this->findModel(Yii::$app->user->id);
        return $this->render('profile', ['model' => $model]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
