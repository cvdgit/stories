<?php

namespace backend\models\answer;

use yii\base\Model;

class DefaultAnswerModel extends Model
{

    public $name;
    public $correct;
    public $description;
    public $order;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'description'], 'string', 'max' => 255],
            [['correct', 'order'], 'integer'],
        ];
    }
}
