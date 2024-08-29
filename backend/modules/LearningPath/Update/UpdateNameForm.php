<?php

declare(strict_types=1);

namespace backend\modules\LearningPath\Update;

use yii\base\Model;

class UpdateNameForm extends Model
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
