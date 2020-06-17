<?php

namespace frontend\controllers;

use common\models\User;
use common\models\UserNotification;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class NotificationController extends Controller
{

    public function actionUnread()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userID = Yii::$app->user->id;
        $user = User::findModel($userID);
        UserNotification::markAllAsRead($user->id);
        return $user->getLastUserNotification();
    }

}