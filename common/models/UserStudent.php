<?php

namespace common\models;

use DomainException;
use frontend\models\UserStudentForm;
use modules\edu\models\StudentLogin;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
 * @property StudentLogin $studentLogin
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
            'name' => 'Имя',
            'birth_date' => 'Дата рождения',
            'created_at' => 'Дата создания',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserQuestionHistories()
    {
        return $this->hasMany(UserQuestionHistory::class, ['student_id' => 'id']);
    }

    /**
     * @return ActiveQuery
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

    public function userOwned(int $userId): bool
    {
        return (int)$this->user_id === $userId;
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

    public function getStudentName()
    {
        return $this->isMain() ? $this->user->getProfileName() : $this->name;
    }

    public function getStudentLogin(): ActiveQuery
    {
        return $this->hasOne(StudentLogin::class, ['student_id' => 'id']);
    }

    public function updateStudent(UserStudentForm $form): void
    {
        $this->name = $form->name;
        $this->birth_date = $form->birth_date;
    }
}
