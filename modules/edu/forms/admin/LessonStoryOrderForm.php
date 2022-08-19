<?php

declare(strict_types=1);

namespace modules\edu\forms\admin;

use yii\base\Model;

class LessonStoryOrderForm extends Model
{

    public $story_ids = [];

    public function rules(): array
    {
        return [
            ['story_ids', 'required'],
            ['story_ids', 'each', 'rule' => ['integer']],
        ];
    }
}
