<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_notification".
 *
 * @property int $notification_id
 * @property int $user_id
 * @property int $read
 *
 * @property Notification $notification
 * @property User $user
 */
class UserNotification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notification_id', 'user_id'], 'required'],
            [['notification_id', 'user_id', 'read'], 'integer'],
            [['notification_id', 'user_id'], 'unique', 'targetAttribute' => ['notification_id', 'user_id']],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => Notification::class, 'targetAttribute' => ['notification_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'user_id' => 'User ID',
            'read' => 'Read',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notification::class, ['id' => 'notification_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function create(int $notificationID, array $users)
    {
        $command = Yii::$app->db->createCommand();
        $columns = ['notification_id', 'user_id'];
        $rows = [];
        foreach ($users as $userID) {
            $rows[] = [$notificationID, $userID];
        }
        $command->batchInsert(self::tableName(), $columns, $rows);
        $command->execute();
    }

    public static function markAllAsRead(int $userID)
    {
        $command = Yii::$app->db->createCommand();
        $command->update(self::tableName(), ['read' => 1], '`user_id` = :user AND `read` = :read', [':user' => $userID, ':read' => 0]);
        $command->execute();
    }

}
