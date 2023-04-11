<?php

declare(strict_types=1);

namespace modules\edu\forms\admin;

use yii\base\Model;

class LessonAccessForm extends Model
{
    public $action;
    public $lessonIds = [];
    public $accessTypes = [];

    public function rules(): array
    {
        return [
            ['action', 'string'],
            ['lessonIds', 'each', 'rule' => ['integer']],
            ['accessTypes', 'each', 'rule' => ['string']],
        ];
    }
}
