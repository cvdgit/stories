<?php

namespace common\services;

use common\models\NotificationModel;
use common\models\User;
use common\models\UserNotification;

class NotificationService
{

    public function create(string $text)
    {
        $notification = new NotificationModel();
        $notification->text = $text;
        return $notification->createNotification();
    }

    public function sendToAllUsers(NotificationModel $notification)
    {
        $userModels = User::find()->activeUsers()->all();
        $users = array_map(function(User $user) {
            return $user->id;
        }, $userModels);
        $this->send($notification, $users);
    }

    protected function send(NotificationModel $notification, array $users)
    {
        $model = $notification->createNotification();
        UserNotification::create($model->id, $users);
    }

}