<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\TeacherAccess;

use yii\base\Model;

class RevokeAccessForm extends Model
{
    public $classBookId;
    public $teacherId;

    public function rules(): array
    {
        return [
            [['classBookId', 'teacherId'], 'required'],
            [['classBookId', 'teacherId'], 'integer'],
        ];
    }
}
