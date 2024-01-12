<?php

declare(strict_types=1);

namespace backend\Story\Tests\UpdatePassTestsRepeat;

use yii\base\Model;

class UpdatePassTestsRepeatForm extends Model
{
    public $questionId;
    public $repeat;

    public function rules(): array
    {
        return [
            [["questionId", "repeat"], "required"],
            ["questionId", "integer"],
            ["repeat", "integer", "min" => 0, "max" => 5],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            "repeat" => "Повторов",
        ];
    }
}
