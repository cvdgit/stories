<?php

declare(strict_types=1);

namespace backend\Story\Tests\UpdateTestsRepeat;

use yii\base\Model;

class UpdateTestsRepeatForm extends Model
{
    public $testId;
    public $repeat;

    public function rules(): array
    {
        return [
            [["testId", "repeat"], "required"],
            ["testId", "integer"],
            ["repeat", "integer", "min" => 1, "max" => 5],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            "repeat" => "Повторов",
        ];
    }
}
