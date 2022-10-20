<?php

namespace modules\edu\models;

use common\models\User;
use common\models\UserStudent;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "edu_class_book".
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property int $class_id
 * @property int $created_at
 *
 * @property EduClass $class
 * @property EduClassBookProgram[] $eduClassBookPrograms
 * @property EduClassBookStudent[] $eduClassBookStudents
 * @property EduClassProgram[] $classPrograms
 * @property EduStudent[] $students
 * @property User $user
 */
class EduClassBook extends ActiveRecord
{

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'classPrograms',
                    'students',
                ],
            ],
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'edu_class_book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'user_id', 'class_id'], 'required'],
            [['user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['class_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduClass::class, 'targetAttribute' => ['class_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'user_id' => 'User ID',
            'class_id' => 'Класс',
            'created_at' => 'Created At',
        ];
    }

    public function getEduClassBookPrograms(): ActiveQuery
    {
        return $this->hasMany(EduClassBookProgram::class, ['class_book_id' => 'id']);
    }

    public function getEduClassBookStudents(): ActiveQuery
    {
        return $this->hasMany(EduClassBookStudent::class, ['class_book_id' => 'id']);
    }

    public function getClassPrograms(): ActiveQuery
    {
        return $this->hasMany(EduClassProgram::class, ['id' => 'class_program_id'])
            ->viaTable('edu_class_book_program', ['class_book_id' => 'id']);
    }

    public function getStudents(): ActiveQuery
    {
        return $this->hasMany(EduStudent::class, ['id' => 'student_id'])
            ->viaTable('edu_class_book_student', ['class_book_id' => 'id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getClass(): ActiveQuery
    {
        return $this->hasOne(EduClass::class, ['id' => 'class_id']);
    }

    /**
     * @param string $name
     * @param int $userId
     * @param int $classId
     * @return static
     */
    public static function create(string $name, int $userId, int $classId): self
    {
        $model = new self();
        $model->name = $name;
        $model->user_id = $userId;
        $model->class_id = $classId;
        return $model;
    }

    public function updateClassBook(string $name, int $classId): void
    {
        $this->name = $name;
        $this->class_id = $classId;
    }

    public function addClassPrograms(array $classProgramIds): void
    {
        $this->classPrograms = $classProgramIds;
    }

    public function addStudent(int $studentId): void
    {
        $this->students = array_unique(array_merge(
            array_map(static function($student) {
                return $student->id;
            }, $this->students),
            [$studentId]));
    }

    public static function findClassBook(int $id, int $userId): ?self
    {
        return self::findOne(['id' => $id, 'user_id' => $userId]);
    }

    public function getClassProgramIds(): array
    {
        return array_column($this->classPrograms, 'id');
    }

    public static function findTeacherClassBooks(int $userId): ActiveQuery
    {
        return self::find()
            ->where(['user_id' => $userId]);
    }

    public function getStudentCount(): int
    {
        return $this->getStudents()->count();
    }
}
