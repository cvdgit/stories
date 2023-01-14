<?php

declare(strict_types=1);

namespace backend\forms;

use yii\base\Model;

class FragmentListSearch extends Model
{
    public $my_lists = true;
    public $for_current_test = true;

    public function rules(): array
    {
        return [
            [['my_lists', 'for_current_test'], 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'my_lists' => 'Мои списки',
            'for_current_test' => 'Из текущего теста',
        ];
    }
}
