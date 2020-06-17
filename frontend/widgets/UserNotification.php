<?php

namespace frontend\widgets;

use Yii;
use yii\base\Widget;

class UserNotification extends Widget
{

    public function run()
    {
        //
        return $this->render('user_notification', [
            'count' => $this->getUserNotificationCount(),
        ]);
    }

    protected function getUserNotificationCount()
    {
        $user = Yii::$app->user->identity;
        $count = $user->getUnreadUserNotificationCount();
        return $count;
    }

}