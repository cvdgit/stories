<?php

namespace modules\edu\models;

use common\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "edu_user_access".
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property int $created_at
 *
 * @property User $user
 */
class EduUserAccess extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'edu_user_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'created_at'], 'required'],
            [['user_id', 'status', 'created_at'], 'integer'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function findUserAccess(int $userId): ?self
    {
        return self::find()
            ->where(['user_id' => $userId])
            ->andWhere(['status' => 1])
            ->one();
    }
}
