<?php


namespace backend\helpers;


use common\models\Payment;
use common\models\Story;
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
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(CURDATE())'))
            ->andWhere(['status' => Story::STATUS_PUBLISHED])
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

}