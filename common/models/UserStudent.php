<?php

namespace common\models;

use DomainException;
use frontend\models\UserStudentForm;
use modules\edu\models\EduClass;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassBookStudent;
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
 * @property string $birth_date
 * @property int $class_id
 *
 * @property EduClassBook[] $classBooks
 * @property EduClassBookStudent[] $eduClassBookStudents
 * @property StudentLogin $studentLogin
 * @property StudentQuestionProgress[] $studentQuestionProgresses
 * @property User $user
 * @property UserQuestionHistory[] $userQuestionHistories
 * @property EduClass $class
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

    /**
     * @param int $userId
     * @param string $name
     * @param int $status
     * @param int|null $classId
     * @param string|null $birth_date
     * @return UserStudent
     */
    private static function create(int $userId, string $name, int $status, int $classId = null, string $birth_date = null): UserStudent
    {
        $model = new self();
        $model->user_id = $userId;
        $model->name = $name;
        $model->class_id = $classId;
        $model->birth_date = $birth_date;
        $model->status = $status;
        return $model;
    }

    public function userOwned(int $userId): bool
    {
        return (int)$this->user_id === $userId;
    }

    public static function createStudent(int $userId, string $name, int $classId, string $birth_date = null): UserStudent
    {
        return self::create($userId, $name,self::STATUS_MAIN, $classId, $birth_date);
    }

    public static function createMain(int $userId, string $name): UserStudent
    {
        return self::create($userId, $name,self::STATUS_MAIN);
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

    public function updateStudent(string $name, int $classId, string $birthDate = null): void
    {
        $this->name = $name;
        $this->birth_date = $birthDate;
        $this->class_id = $classId;
    }

    public function getClass(): ActiveQuery
    {
        return $this->hasOne(EduClass::class, ['id' => 'class_id']);
    }

    public function getClassBooks(): ActiveQuery
    {
        return $this->hasMany(EduClassBook::class, ['id' => 'class_book_id'])
            ->viaTable('edu_class_book_student', ['student_id' => 'id']);
    }

    public function getEduClassBookStudents(): ActiveQuery
    {
        return $this->hasMany(EduClassBookStudent::class, ['student_id' => 'id']);
    }

    public static function findMainByUserId(int $userId): ?self
    {
        /** @var UserStudent|null $model */
        $model = self::find()
            ->where([
                'user_id' => $userId,
                'status' => self::STATUS_MAIN,
            ])
            ->one();
        return $model;
    }
}
