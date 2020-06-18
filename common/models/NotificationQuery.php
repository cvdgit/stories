<?php

namespace common\models;

use yii\db\ActiveQuery;

class NotificationQuery extends ActiveQuery
{

    public function unread(int $userID)
    {
        return $this->innerJoinWith(['userNotifications'])
            ->andWhere(['user_id' => $userID])
            ->andWhere(['read' => 0]);
    }

    public function unreadCount(int $userID)
    {
        return $this->unread($userID)->count();
    }

    public function last(int $userID)
    {
        return $this->innerJoinWith(['userNotifications'])
            ->andWhere(['user_id' => $userID])
            ->orderBy(['notification.created_at' => SORT_DESC])
            ->limit(10);
    }

}