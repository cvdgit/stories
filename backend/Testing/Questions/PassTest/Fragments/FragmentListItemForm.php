<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest\Fragments;

use yii\base\Model;

class FragmentListItemForm extends Model
{
    public $id;
    public $title;
    public $correct;

    public function rules(): array
    {
        return [
            ['title', 'string', 'max' => 50],
        ];
    }
}
