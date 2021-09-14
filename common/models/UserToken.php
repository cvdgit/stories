<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_token".
 *
 * @property string $token
 * @property int $user_id
 * @property int $expired_at
 *
 * @property User $user
 */
class UserToken extends ActiveRecord
{

    public static function tableName()
    {
        return 'user_token';
    }

    public function rules()
    {
        return [
            [['token', 'expired_at'], 'required'],
            [['user_id', 'expired_at'], 'integer'],
            [['token'], 'string', 'max' => 50],
            [['token'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'token' => 'Token',
            'user_id' => 'User ID',
            'expired_at' => 'Expired At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function findByToken(string $token): ?self
    {
        return self::findOne($token);
    }

    public function isExpired(): bool
    {
        return $this->expired_at < time();
    }

    public function resetToken(): void
    {
        $this->delete();
    }

    public static function create(int $userID): self
    {
        $model = new self;
        $model->token = Yii::$app->security->generateRandomString();
        $model->user_id = $userID;
        $model->expired_at = time() + (10 * 60);
        return $model;
    }

    public function getLoginUrl(StudyTask $task = null): string
    {
        $url = ['auth/token', 'token' => $this->token];
        if ($task !== null) {
            $url['task'] = $task->id;
        }
        return Yii::$app->urlManagerFrontend->createUrl($url);
    }
}
