<?php

declare(strict_types=1);

namespace backend\modules\LearningPath\Create;

use yii\base\Model;

class CreateForm extends Model
{
    public $name;

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],
        ];
    }
}
