<?php

declare(strict_types=1);

namespace modules\edu\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property int $user_id [int(11)]
 * @property int $status [tinyint(3)]
 * @property string $name [varchar(255)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 * @property string $birth_date [date]
 * @property int $class_id [int(11)]
 *
 * @property EduUser $user
 * @property EduClass $class
 * @property EduParentInvite $parentInvite
 * @property StudentLogin $studentLogin
 */
class EduStudent extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%user_student}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'ФИО',
            'created_at' => 'Дата создания',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(EduUser::class, ['id' => 'user_id']);
    }

    public function getClass(): ActiveQuery
    {
        return $this->hasOne(EduClass::class, ['id' => 'class_id']);
    }

    public function getParentInvite(): ActiveQuery
    {
        return $this->hasOne(EduParentInvite::class, ['student_id' => 'id']);
    }

    public function haveInvitedParent(): bool
    {
        return !empty($this->parentInvite) && $this->parentInvite->isActive();
    }

    public function getStudentLogin(): ActiveQuery
    {
        return $this->hasOne(StudentLogin::class, ['student_id' => 'id']);
    }

    public static function createByParent(int $userId, string $name, int $classId): self
    {
        $model = new self();
        $model->user_id = $userId;
        $model->name = $name;
        $model->class_id = $classId;
        $model->status = 1;
        return $model;
    }
}
