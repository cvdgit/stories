<?php

namespace common\models;

use DomainException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_child".
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property string $name
 * @property int $age_year
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class UserStudent extends ActiveRecord
{

    const STATUS_MAIN = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_student';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'name' => 'Name',
            'age_year' => 'Age Year',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Пользователь не найден.');
    }

    public static function create(int $userID, string $name, int $age_year, $status = 0)
    {
        $model = new self();
        $model->user_id = $userID;
        $model->name = $name;
        $model->age_year = $age_year;
        $model->status = $status;
        return $model;
    }

    public function userOwned(int $userID)
    {
        return (int)$this->user_id === $userID;
    }

    public static function createMain(int $userID, string $name, int $age_year)
    {
        return self::create($userID, $name, $age_year, self::STATUS_MAIN);
    }

    public function isMain()
    {
        return (int)$this->status === self::STATUS_MAIN;
    }

}
