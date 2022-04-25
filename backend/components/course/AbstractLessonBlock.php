<?php

namespace backend\components\course;

use yii\base\Model;

abstract class AbstractLessonBlock extends Model
{

    public $slide_id;
    public $order;
    public $data;

    public function rules(): array
    {
        return [
            [['slide_id', 'order'], 'integer'],
            ['data', 'text'],
        ];
    }
}
