<?php

declare(strict_types=1);

namespace modules\edu\forms\admin;

use yii\base\Model;

class TopicLessonOrderForm extends Model
{

    public $lesson_ids = [];

    public function rules(): array
    {
        return [
            ['lesson_ids', 'required'],
            ['lesson_ids', 'each', 'rule' => ['integer']],
        ];
    }
}
