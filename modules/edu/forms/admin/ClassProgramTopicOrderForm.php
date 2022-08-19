<?php

declare(strict_types=1);

namespace modules\edu\forms\admin;

use yii\base\Model;

class ClassProgramTopicOrderForm extends Model
{

    public $topic_ids = [];

    public function rules(): array
    {
        return [
            ['topic_ids', 'required'],
            ['topic_ids', 'each', 'rule' => ['integer']],
        ];
    }
}
