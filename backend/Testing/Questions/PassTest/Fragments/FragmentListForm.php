<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest\Fragments;

use yii\base\Model;

class FragmentListForm extends Model
{
    public $name;

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 50],
        ];
    }
}
