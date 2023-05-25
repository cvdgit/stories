<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\ManageTopics;

use yii\base\Model;

class ManageTopicForm extends Model
{
    public $class_book_id;

    public function rules(): array
    {
        return [
            [['class_book_id'], 'required'],
            [['class_book_id'], 'integer'],
        ];
    }
}
