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
 * @property int $created_at
 * @property int $updated_at
 * @property string $birth_date;
 *
 * @property UserQuestionHistory[] $userQuestionHistories
 * @property User $user
 */
class UserStudent extends ActiveRecord
{

    const STATUS_STUDENT = 0;
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
            [['status'], 'integer'],
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
            'birth_date' => 'Дата рождения',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserQuestionHistories()
    {
        return $this->hasMany(UserQuestionHistory::class, ['student_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentQuestionProgresses()
    {
        return $this->hasMany(StudentQuestionProgress::class, ['student_id' => 'id']);
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Пользователь не найден.');
    }

    public static function create(int $userID, string $name, $birth_date, $status)
    {
        $model = new self();
        $model->user_id = $userID;
        $model->name = $name;
        $model->birth_date = $birth_date;
        $model->status = $status;
        return $model;
    }

    public function userOwned(int $userID)
    {
        return (int)$this->user_id === $userID;
    }

    public static function createStudent(int $userID, string $name, string $birth_date)
    {
        return self::create($userID, $name, $birth_date, self::STATUS_STUDENT);
    }

    public static function createMain(int $userID, string $name, string $birth_date = null)
    {
        return self::create($userID, $name, $birth_date, self::STATUS_MAIN);
    }

    public function isMain()
    {
        return (int)$this->status === self::STATUS_MAIN;
    }

    public function getProgress(int $testID)
    {
        $model = $this->getStudentQuestionProgresses()
            ->andWhere('test_id = :test', [':test' => $testID])
            ->one();
        if ($model === null) {
            return 0;
        }
        return $model->progress;
    }

}
