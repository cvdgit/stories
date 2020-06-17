<?php

namespace common\models;

use yii\db\ActiveQuery;

class NotificationQuery extends ActiveQuery
{

    public function unread()
    {
        return $this->innerJoinWith(['userNotifications'])->andWhere(['read' => 0]);
    }

    public function unreadCount()
    {
        return $this->unread()->count();
    }

    public function last()
    {
        return $this->innerJoinWith(['userNotifications'])->orderBy(['notification.created_at' => SORT_DESC])->limit(10);
    }

}