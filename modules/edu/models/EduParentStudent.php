<?php

declare(strict_types=1);

namespace modules\edu\models;

use yii\db\ActiveRecord;

/**
 * @property int $parent_id [int(11)]
 * @property int $student_id [int(11)]
 */
class EduParentStudent extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%edu_parent_student}}';
    }

    public static function create(int $parentId, int $studentId): self
    {
        $model = new self();
        $model->parent_id = $parentId;
        $model->student_id = $studentId;
        return $model;
    }
}
