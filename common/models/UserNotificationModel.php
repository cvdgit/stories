<?php

namespace common\models;

use yii\base\Model;

class UserNotificationModel extends Model
{

    public $notification_id;
    public $users;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function createUserNotification()
    {

    }

}