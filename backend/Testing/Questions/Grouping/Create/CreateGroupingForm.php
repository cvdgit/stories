<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Grouping\Create;

use yii\base\Model;

class CreateGroupingForm extends Model
{
    public $name;
    public $payload;

    public function rules(): array
    {
        return [
            [["name", "payload"], "required"],
            ["payload", "safe"],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
        ];
    }
}
