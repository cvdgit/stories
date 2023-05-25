<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\ManageTopics;

use yii\base\Model;

class TopicAccessForm extends Model
{
    public $class_program_id;
    public $topic_id;

    public function rules(): array
    {
        return [
            [['class_program_id', 'topic_id'], 'required'],
            [['class_program_id', 'topic_id'], 'integer'],
        ];
    }
}
