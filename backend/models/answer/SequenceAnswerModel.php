<?php

namespace backend\models\answer;

use yii\base\Model;

class SequenceAnswerModel extends Model
{

    public $name;
    public $order;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['order'], 'integer'],
        ];
    }
}
