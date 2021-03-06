<?php

namespace backend\helpers;

use common\models\Payment;
use common\models\story\StoryStatus;
use common\models\User;
use yii\db\Expression;
use yii\db\Query;

class SummaryHelper
{

    public static function activatedSubscriptions()
    {
        return (new Query())->from('{{%payment}}')
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(CURDATE())'))
            ->andWhere(['state' => Payment::STATUS_VALID])
            ->count('id');
    }

    public static function publishedStories()
    {
        return (new Query())->from('{{%story}}')
            ->where(new Expression('`published_at` >= UNIX_TIMESTAMP(CURDATE())'))
            ->andWhere(['status' => StoryStatus::PUBLISHED])
            ->count('id');
    }

    public static function registeredUsers()
    {
        return (new Query())->from('{{%user}}')
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(CURDATE())'))
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->count('id');
    }

    public static function commentsWritten()
    {
        return (new Query())->from('{{%comment}}')
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(CURDATE())'))
            ->count('id');
    }

    public static function viewedStories()
    {
        $query = (new Query())
            ->select(new Expression('COUNT(DISTINCT story_id) AS cnt'))
            ->from('{{%story_statistics}}')
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(CURDATE())'))
            ->groupBy(['story_id', 'session']);

        $query2 = (new Query())
            ->select(new Expression('COUNT(DISTINCT story_id) AS cnt'))
            ->from('{{%story_readonly_statistics}}')
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(CURDATE())'))
            ->groupBy(['story_id']);

        $query->union($query2, true);
        return (new Query())->from(['a' => $query])->sum('a.cnt');
    }

    public static function activePayments()
    {
        return (new Query())
            ->from('{{%payment}}')
            ->where(['<=', '{{%payment}}.payment', new Expression('NOW()')])
            ->andWhere(['>=', '{{%payment}}.finish', new Expression('NOW()')])
            ->andWhere('{{%payment}}.state = :valid', [':valid' => Payment::STATUS_VALID])
            ->count('{{%payment}}.id');
    }
}
