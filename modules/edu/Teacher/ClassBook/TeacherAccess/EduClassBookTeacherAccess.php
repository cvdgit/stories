<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\TeacherAccess;

use common\models\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $class_book_id
 * @property int $teacher_id
 * @property int $created_at
 * @property int $access_type
 *
 * @property-read User $teacher
 */
class EduClassBookTeacherAccess extends ActiveRecord
{
    public function getTeacher(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'teacher_id']);
    }
}
