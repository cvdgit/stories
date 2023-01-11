<?php

declare(strict_types=1);

namespace backend\forms;

use yii\base\Model;

class FragmentListItemForm extends Model
{
    public $name;

    public function rules(): array
    {
        return [
            ['name', 'string', 'max' => 50],
        ];
    }
}
